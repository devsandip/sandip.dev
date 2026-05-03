"""GET /r/{token} — read-only HTML view of a submission."""
import os
import html
import boto3
from botocore.exceptions import ClientError

TABLE_NAME = os.environ["TABLE_NAME"]
ddb = boto3.resource("dynamodb")
table = ddb.Table(TABLE_NAME)


def _page(title, body):
    return f"""<!DOCTYPE html><html><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{html.escape(title)}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&family=Fraunces:opsz,wght@9..144,400;9..144,500&display=swap" rel="stylesheet">
<style>
:root {{ --paper:#F6F6F7;--paper-2:#EFEFF1;--ink:#0A0A0B;--ink-soft:#4A4A52;--ink-softer:#8A8A92;--rule:#E2E2E5;--accent:#1F4DDA; }}
*{{box-sizing:border-box;margin:0;padding:0}}
body{{background:var(--paper);color:var(--ink);font-family:'Inter',sans-serif;font-size:15px;line-height:1.55;-webkit-font-smoothing:antialiased}}
header{{border-bottom:1px solid var(--rule);background:var(--paper);padding:18px 32px}}
header .inner{{max-width:880px;margin:0 auto;display:flex;align-items:center;gap:10px;font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:600}}
header .mark{{width:22px;height:22px;background:var(--ink);color:var(--paper);display:grid;place-items:center;font-size:11px;font-weight:700}}
main{{max-width:760px;margin:0 auto;padding:48px 32px 80px}}
h1{{font-size:36px;font-weight:800;letter-spacing:-0.025em;line-height:1.05;margin-bottom:8px}}
h1 em{{font-family:'Fraunces',serif;font-style:italic;color:var(--accent);font-weight:500}}
.meta{{font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--ink-softer);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:32px}}
.banner{{background:var(--paper-2);padding:14px 18px;border-left:2px solid var(--accent);font-size:13px;color:var(--ink-soft);margin-bottom:32px}}
.section{{border-top:1px solid var(--rule);padding:22px 0}}
.section:last-of-type{{border-bottom:1px solid var(--rule)}}
.section h3{{font-family:'JetBrains Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-softer);margin-bottom:14px}}
.row{{display:grid;grid-template-columns:160px 1fr;gap:16px;padding:8px 0;font-size:14px;align-items:baseline}}
.row .k{{color:var(--ink-softer);font-family:'JetBrains Mono',monospace;font-size:12px;letter-spacing:0.02em}}
.row .v{{color:var(--ink);word-break:break-word}}
.row .v a{{color:var(--accent);text-decoration:underline}}
.why-block{{padding:16px;background:var(--paper-2);font-size:14px;line-height:1.6;white-space:pre-wrap;margin-top:8px}}
footer{{border-top:1px solid var(--rule);padding:32px;text-align:center;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--ink-softer);text-transform:uppercase;letter-spacing:0.04em}}
.empty{{text-align:center;padding:80px 0}}
.empty h1{{font-size:28px;margin-bottom:12px}}
.empty p{{color:var(--ink-soft)}}
</style></head><body>
<header><div class="inner"><div class="mark">SD</div><span>sandip.dev</span></div></header>
{body}
<footer>© 2026 Sandip Dev · this is a private read-only view of your submission</footer>
</body></html>"""


def _resp_html(status, body):
    return {
        "statusCode": status,
        "headers": {
            "Content-Type": "text/html; charset=utf-8",
            "Cache-Control": "private, no-store",
        },
        "body": body,
    }


def _row(k, v):
    if not v:
        return ""
    return f'<div class="row"><div class="k">{html.escape(k)}</div><div class="v">{v}</div></div>'


def _link(url):
    return f'<a href="{html.escape(url)}" target="_blank" rel="noopener">{html.escape(url)}</a>'


def handler(event, context):
    path_params = event.get("pathParameters") or {}
    token = path_params.get("token", "")
    if not token or len(token) != 32:
        return _resp_html(404, _page("Not found",
            '<main><div class="empty"><h1>Link not found</h1>'
            '<p>Double-check your link, or ask Sandip for a fresh one.</p></div></main>'))

    try:
        r = table.get_item(Key={"token": token})
    except ClientError as e:
        print(f"DDB get_item failed: {e}")
        return _resp_html(500, _page("Error",
            '<main><div class="empty"><h1>Something broke</h1><p>Try again in a minute.</p></div></main>'))

    item = r.get("Item")
    if not item:
        return _resp_html(404, _page("Not found",
            '<main><div class="empty"><h1>Link not found</h1>'
            '<p>This link may have expired or never existed.</p></div></main>'))

    body = (
        f'<main>'
        f'<h1>Your <em>referral</em> request</h1>'
        f'<div class="meta">submitted {html.escape(item.get("submitted_at",""))}</div>'
        f'<div class="banner">This is a private, read-only view of what you submitted. Bookmark this URL — anyone with the link can view it.</div>'

        f'<div class="section"><h3>// about you</h3>'
        + _row("Name", html.escape(item.get("full_name","")))
        + _row("Email", html.escape(item.get("email","")))
        + _row("Phone", f'{html.escape(item.get("phone_cc",""))} {html.escape(item.get("phone",""))}')
        + _row("LinkedIn", _link(item.get("linkedin_url","")))
        + _row("Experience", html.escape(item.get("experience","")) + " years")
        + '</div>'

        f'<div class="section"><h3>// the role</h3>'
        + _row("Request type", html.escape(item.get("request_type","")))
        + _row("Target company", html.escape(item.get("target_company","")))
        + _row("Target role", html.escape(item.get("target_role","")))
        + _row("Job posting", _link(item.get("job_url","")))
        + '</div>'

        f'<div class="section"><h3>// why a good fit</h3>'
        f'<div class="why-block">{html.escape(item.get("why",""))}</div>'
        + '</div>'

        f'<div class="section"><h3>// resume</h3>'
        + _row("Resume link", _link(item.get("resume_url","")))
        + '</div>'

        f'</main>'
    )
    return _resp_html(200, _page("Your referral request — sandip.dev", body))
