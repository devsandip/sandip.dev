# Referral backend — `sandip.dev`

DynamoDB + Lambda (Python 3.12) + API Gateway HTTP API + SES (auto-verified via Route53).

## What gets created

- `sandip-dev-referrals` DynamoDB table (PAY_PER_REQUEST, 365-day TTL on `expires_at`).
- Two Lambdas: `SubmitFn` (POST /referral, validates + emails) and `ViewFn` (GET /r/{token}, HTML).
- HTTP API with CORS for `https://sandip.dev` + `http://localhost:8000`.
- SES domain identity for `sandip.dev` with DKIM CNAMEs auto-added to Route53, plus `mail.sandip.dev` MAIL FROM.

## First deploy

```bash
aws sso login --profile admin
cd infra
./deploy.sh
```

The first run prompts to confirm the change set. Outputs include `SubmitEndpoint` and `ViewBase` — copy those into `referral.html` (replace `REPLACE_WITH_API_URL`).

## SES sandbox note

New AWS accounts start in the SES **sandbox** — SES will only send to verified addresses. To test end-to-end while in sandbox you must verify `me@sandip.dev` _and_ any test submitter address. Once you've smoke-tested, request SES production access from the SES console (24h-ish turnaround). The DKIM-verified domain gives you the right reputation footing.

## Subsequent deploys

```bash
./deploy.sh --no-confirm
```

## Tear down

```bash
aws cloudformation delete-stack --stack-name sandip-dev-referral --profile admin --region us-east-1
```

(DynamoDB has PITR enabled — delete manually if you want to drop backups.)
