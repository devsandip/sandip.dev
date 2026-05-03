"""POST /referral — validate, persist to DynamoDB, send confirmation email."""
import json
import os
import re
import uuid
import time
import socket
import urllib.request
import urllib.error
from datetime import datetime, timezone, timedelta

import boto3
from botocore.exceptions import ClientError

# ─── Config (env) ────────────────────────────────────────────────
TABLE_NAME = os.environ["TABLE_NAME"]
SES_FROM   = os.environ["SES_FROM"]            # e.g. "Sandip Dev <noreply@sandip.dev>"
ADMIN_TO   = os.environ["ADMIN_TO"]            # me@sandip.dev
VIEW_BASE  = os.environ["VIEW_BASE"]           # https://api.sandip.dev/r/   or API GW URL
ALLOW_ORIGIN = os.environ.get("ALLOW_ORIGIN", "*")
TTL_DAYS   = int(os.environ.get("TTL_DAYS", "365"))

ddb = boto3.resource("dynamodb")
table = ddb.Table(TABLE_NAME)
ses = boto3.client("ses")

# ─── Constants ───────────────────────────────────────────────────
KNOWN_JOB_DOMAINS = {
    "amazon.jobs", "jobs.amazon.com", "linkedin.com", "glassdoor.com",
    "indeed.com", "jobs.lever.co", "boards.greenhouse.io", "jobs.ashbyhq.com",
    "smartrecruiters.com", "myworkdayjobs.com", "careers.google.com",
    "careers.microsoft.com", "metacareers.com", "careers.netflix.com",
    "jobs.netflix.com", "careers.airbnb.com", "jobs.stripe.com",
    "careers.uber.com", "wellfound.com",
}
RESUME_DOMAINS = {
    "docs.google.com", "drive.google.com", "dropbox.com",
    "onedrive.live.com", "1drv.ms", "icloud.com", "box.com",
}
DISPOSABLE_EMAIL_DOMAINS = {
    "mailinator.com", "10minutemail.com", "tempmail.com", "guerrillamail.com",
    "throwawaymail.com", "yopmail.com", "trashmail.com", "fakeinbox.com",
    "getnada.com", "tempinbox.com", "sharklasers.com", "maildrop.cc",
    "mintemail.com", "mohmal.com", "spam4.me", "tempr.email", "dispostable.com",
    "nada.email", "emailondeck.com",
}
PHONE_LEN = {"+1":10,"+91":10,"+44":10,"+61":9,"+971":9,"+966":9,"+27":9,
             "+65":8,"+86":11,"+81":10,"+49":11,"+33":9}

# ─── Helpers ─────────────────────────────────────────────────────
def _resp(status, body):
    return {
        "statusCode": status,
        "headers": {
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": ALLOW_ORIGIN,
            "Access-Control-Allow-Methods": "POST,OPTIONS",
            "Access-Control-Allow-Headers": "Content-Type",
        },
        "body": json.dumps(body),
    }

def _head_check(url, timeout=4):
    """HEAD-check a URL. Returns (ok, final_status_or_error)."""
    try:
        req = urllib.request.Request(url, method="HEAD",
                                     headers={"User-Agent": "Mozilla/5.0 sandip.dev-validator"})
        with urllib.request.urlopen(req, timeout=timeout) as r:
            return (200 <= r.status < 400, r.status)
    except urllib.error.HTTPError as e:
        # some sites reject HEAD; try GET
        try:
            req = urllib.request.Request(url, method="GET",
                                         headers={"User-Agent": "Mozilla/5.0 sandip.dev-validator"})
            with urllib.request.urlopen(req, timeout=timeout) as r:
                return (200 <= r.status < 400, r.status)
        except Exception as e2:
            return (False, str(e2))
    except Exception as e:
        return (False, str(e))

def _mx_exists(domain):
    """Cheap MX check: just confirm DNS resolves something for the domain."""
    try:
        socket.gethostbyname(domain)
        return True
    except Exception:
        return False

def _safe_url(v):
    try:
        from urllib.parse import urlparse
        u = urlparse(v)
        if u.scheme not in ("http","https"): return None
        if not u.netloc: return None
        return u
    except Exception:
        return None

# ─── Validators ──────────────────────────────────────────────────
def validate(payload):
    errors = {}

    # name
    name = (payload.get("full_name") or "").strip()
    if not (2 <= len(name) <= 80) or not re.match(r"^[\w\-'\.\sÀ-ɏ]+$", name) or len(name.split()) < 2:
        errors["name"] = "Please enter your full name (first and last)."

    # linkedin
    li = (payload.get("linkedin_url") or "").strip()
    li_u = _safe_url(li)
    if not li_u or not re.match(r"^([a-z0-9-]+\.)?linkedin\.com$", li_u.netloc, re.I) \
       or not re.match(r"^/in/[A-Za-z0-9\-_%]{3,}/?$", li_u.path):
        errors["linkedin"] = "Must be a linkedin.com/in/<handle> URL."
    else:
        ok, _ = _head_check(li)
        if not ok:
            errors["linkedin"] = "We couldn't reach this LinkedIn profile. Double-check the URL."

    # experience
    if (payload.get("experience") or "") not in {"0-1","2-3","4-6","7-10","11-15","15+"}:
        errors["experience"] = "Pick one of the options."

    # company / role
    if not (2 <= len((payload.get("target_company") or "").strip()) <= 80):
        errors["company"] = "2–80 chars."
    if not (2 <= len((payload.get("target_role") or "").strip()) <= 120):
        errors["role"] = "2–120 chars."

    # job url
    job = (payload.get("job_url") or "").strip()
    job_u = _safe_url(job)
    job_warn = None
    if not job_u:
        errors["job_url"] = "Not a valid URL."
    else:
        host = job_u.netloc.lower()
        if not any(host == d or host.endswith("." + d) for d in KNOWN_JOB_DOMAINS):
            job_warn = "Not a recognized job board domain (allowed)."
        ok, _ = _head_check(job)
        if not ok and "job_url" not in errors:
            errors["job_url"] = "Couldn't reach the job posting URL."

    # why
    why = (payload.get("why") or "").strip()
    if len(why) < 150:
        errors["why"] = f"Minimum 150 characters (you have {len(why)})."
    elif len(why) > 2000:
        errors["why"] = "Maximum 2000 characters."

    # resume
    resume = (payload.get("resume_url") or "").strip()
    resume_u = _safe_url(resume)
    if not resume_u:
        errors["resume"] = "Not a valid URL."
    else:
        host = resume_u.netloc.lower()
        if not any(host == d or host.endswith("." + d) for d in RESUME_DOMAINS):
            errors["resume"] = "Use Google Docs/Drive, OneDrive, Dropbox, or Box."
        else:
            ok, status = _head_check(resume)
            if not ok:
                errors["resume"] = "Couldn't reach this link. Make sure it's set to public/anyone-with-link."

    # email
    email = (payload.get("email") or "").strip().lower()
    if not re.match(r"^[^\s@]+@[^\s@]+\.[^\s@]{2,}$", email):
        errors["email"] = "Not a valid email."
    else:
        domain = email.split("@",1)[1]
        if domain in DISPOSABLE_EMAIL_DOMAINS:
            errors["email"] = "Please use a non-disposable email address."
        elif not _mx_exists(domain):
            errors["email"] = "Email domain doesn't appear to exist."

    # phone
    cc = (payload.get("phone_cc") or "").strip()
    ph = (payload.get("phone") or "").strip()
    digits = re.sub(r"\D","",ph)
    if not cc:
        errors["phone"] = "Pick a country code."
    elif not (6 <= len(digits) <= 15):
        errors["phone"] = "Phone number length is off."
    elif cc in PHONE_LEN and len(digits) != PHONE_LEN[cc]:
        errors["phone"] = f"Expected {PHONE_LEN[cc]} digits for {cc}."

    return errors, job_warn

# ─── Email ───────────────────────────────────────────────────────
def _fmt_email(rec, view_url, *, for_admin):
    lines = [
        f"Name:           {rec['full_name']}",
        f"Email:          {rec['email']}",
        f"Phone:          {rec['phone_cc']} {rec['phone']}",
        f"LinkedIn:       {rec['linkedin_url']}",
        f"Experience:     {rec['experience']} years",
        "",
        f"Request type:   {rec['request_type']}",
        f"Target company: {rec['target_company']}",
        f"Target role:    {rec['target_role']}",
        f"Job posting:    {rec['job_url']}",
        "",
        "Why a good fit:",
        rec["why"],
        "",
        f"Submitted at:   {rec['submitted_at']}",
        f"View link:      {view_url}",
    ]
    body = "\n".join(lines)
    if for_admin:
        subject = f"[Referral] {rec['full_name']} → {rec['target_company']} ({rec['target_role']})"
        body = "New referral request:\n\n" + body + f"\n\nResume URL: {rec['resume_url']}\n"
    else:
        subject = f"Your referral request to {rec['target_company']} — received"
        body = (f"Hi {rec['full_name'].split()[0]},\n\n"
                f"Thanks — I've received your referral request. "
                f"I'll review it personally and get back within 48 hours.\n\n"
                f"Below is a copy of what you submitted. You can also view it anytime at:\n{view_url}\n\n"
                + "─" * 50 + "\n\n" + body + "\n\n— Sandip\n")
    return subject, body

def _send(to_addr, subject, body):
    try:
        ses.send_email(
            Source=SES_FROM,
            Destination={"ToAddresses":[to_addr]},
            Message={
                "Subject":{"Data":subject,"Charset":"UTF-8"},
                "Body":{"Text":{"Data":body,"Charset":"UTF-8"}},
            },
        )
    except ClientError as e:
        print(f"SES send failed for {to_addr}: {e}")

# ─── Handler ─────────────────────────────────────────────────────
def handler(event, context):
    method = (event.get("requestContext",{}).get("http",{}) or {}).get("method") \
             or event.get("httpMethod","POST")
    if method == "OPTIONS":
        return _resp(204, {})

    try:
        payload = json.loads(event.get("body") or "{}")
    except json.JSONDecodeError:
        return _resp(400, {"message":"Invalid JSON."})

    errors, job_warn = validate(payload)
    if errors:
        return _resp(422, {"message":"Some fields could not be verified.","field_errors":errors})

    token = uuid.uuid4().hex
    now = datetime.now(timezone.utc)
    expires = int((now + timedelta(days=TTL_DAYS)).timestamp())

    rec = {
        "token": token,
        "submitted_at": now.isoformat(),
        "expires_at": expires,
        "request_type": payload.get("request_type","direct"),
        "full_name": payload["full_name"].strip(),
        "linkedin_url": payload["linkedin_url"].strip(),
        "experience": payload["experience"],
        "target_company": payload["target_company"].strip(),
        "target_role": payload["target_role"].strip(),
        "job_url": payload["job_url"].strip(),
        "job_url_warning": job_warn or "",
        "why": payload["why"].strip(),
        "resume_url": payload["resume_url"].strip(),
        "email": payload["email"].strip().lower(),
        "phone_cc": payload["phone_cc"],
        "phone": payload["phone"].strip(),
    }

    try:
        table.put_item(Item=rec)
    except ClientError as e:
        print(f"DDB put_item failed: {e}")
        return _resp(500, {"message":"Could not save your submission. Please retry."})

    view_url = VIEW_BASE.rstrip("/") + "/" + token

    # admin email (full)
    subj_a, body_a = _fmt_email(rec, view_url, for_admin=True)
    _send(ADMIN_TO, subj_a, body_a)
    # user email (no resume_url)
    user_rec = {**rec}
    subj_u, body_u = _fmt_email(user_rec, view_url, for_admin=False)
    body_u = body_u.replace(rec["resume_url"], "(omitted in your copy)")
    _send(rec["email"], subj_u, body_u)

    return _resp(200, {"token": token, "view_url": view_url})
