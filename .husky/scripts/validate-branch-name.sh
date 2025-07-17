#!/bin/sh
branch_name=$(git rev-parse --abbrev-ref HEAD)

if ! echo "$branch_name" | grep -qE '^(master|main|develop|staging){1}$|^(build|chore|ci|docs|feat|fix|hotfix|perf|refactor|revert|test|style|release)/.+$'; then
  echo "❌ Error: Branch name '$branch_name' does not follow our branch naming convention (see Notion for more information)"
  echo "⚠️ It must match the following regex: ^(master|main|develop|staging){1}$|^(build|chore|ci|docs|feat|fix|hotfix|perf|refactor|revert|test|style|release)/.+$"
  exit 1
fi
