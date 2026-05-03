#!/usr/bin/env bash
# One-shot deploy script for the referral backend.
#
# Prereqs: aws CLI, sam CLI, an SSO session via `aws sso login --profile admin`.
#
# Usage:
#   ./deploy.sh                # first-time guided deploy
#   ./deploy.sh --no-confirm   # subsequent deploys

set -euo pipefail

PROFILE="${AWS_PROFILE:-admin}"
REGION="${AWS_REGION:-us-east-1}"
STACK="sandip-dev-referral"

echo "→ Using AWS profile: $PROFILE  region: $REGION"

# Confirm SSO session is alive
if ! aws sts get-caller-identity --profile "$PROFILE" >/dev/null 2>&1; then
  echo "× SSO session not active. Run:  aws sso login --profile $PROFILE"
  exit 1
fi

cd "$(dirname "$0")"

if [[ "${1-}" == "--no-confirm" ]]; then
  CONFIRM_FLAG="--no-confirm-changeset"
else
  CONFIRM_FLAG="--confirm-changeset"
fi

sam build

sam deploy \
  --stack-name "$STACK" \
  --region "$REGION" \
  --profile "$PROFILE" \
  --capabilities CAPABILITY_IAM \
  --resolve-s3 \
  --no-fail-on-empty-changeset \
  $CONFIRM_FLAG

echo
echo "─── Stack outputs ───────────────────────"
aws cloudformation describe-stacks \
  --stack-name "$STACK" \
  --profile "$PROFILE" \
  --region "$REGION" \
  --query 'Stacks[0].Outputs[*].[OutputKey,OutputValue]' \
  --output table

echo
echo "Next steps:"
echo "  1) Wire the API URL into referral.html (replace REPLACE_WITH_API_URL with the SubmitEndpoint above)."
echo "  2) Verify your sender by clicking the SES verification email sent to ${FromEmail:-noreply@sandip.dev},"
echo "     OR if you're still in SES sandbox, also verify ADMIN_TO and any test recipient addresses."
echo "  3) (Optional) Request SES production access in the AWS console once smoke-tested."
