---
name: project-ideate
description: Use when asked to ideate product improvements for the current application, prioritize the top 5 by impact, and create them as Linear projects using the Linear MCP.
---

# Project Ideate

Use this skill when the user wants strategic product improvement ideas for the current codebase/application and wants those ideas created as projects in Linear.

## Goal

Analyze the codebase enough to understand what the application does, infer how real users and the business would experience it, identify the highest-impact improvement opportunities, then create the top 5 as Linear projects using the Linear MCP.

## Workflow

1. **Inspect the application**
   - Review the repository structure, README/docs, routes/controllers, models, UI views, migrations, configuration, and any frontend app areas.
   - Identify the application domain, primary user roles, core workflows, revenue/business workflows, and operational/admin workflows.
   - Do not make code changes unless the user explicitly asks for implementation.

2. **Think from user and business perspectives**
   - User perspective: onboarding, task completion, clarity, speed, trust, accessibility, error handling, mobile/responsiveness, and friction points.
   - Business perspective: revenue enablement, retention, support burden, operational efficiency, compliance/security risk, reporting, scalability, and maintainability.

3. **Generate candidate improvements**
   - Look for improvements across product UX, workflow automation, reporting/analytics, reliability, security, billing/payments, customer communication, admin tooling, and developer/operational maintainability.
   - Ground every idea in evidence from the codebase where possible.
   - Avoid speculative ideas that do not fit the current app or business model.

4. **Prioritize the top 5**
   - Score or reason about each candidate using:
     - User impact
     - Business impact
     - Risk reduction
     - Reach/frequency of use
     - Implementation effort
     - Dependency/complexity
   - Select exactly 5 improvements with the strongest overall impact, balancing quick wins and strategic initiatives.

5. **Create Linear projects**
   - Use the Linear MCP to create one Linear project for each of the top 5 improvements.
   - If the Linear workspace, team, status, or project fields are ambiguous, inspect available Linear MCP resources first. Ask the user only if required fields cannot be inferred.
   - Each Linear project should include:
     - Clear, outcome-oriented title
     - Concise problem statement
     - Why it matters for users
     - Why it matters for the business
     - Proposed scope / key capabilities
     - Success metrics
     - Suggested priority ranking from 1 to 5
     - Evidence or codebase areas that motivated the idea
     - Initial risks, dependencies, or open questions
   - Each project should receive a 'crm' label

## Linear project description template

Use this structure for each Linear project description:

```markdown
## Problem
Describe the user/business problem this project addresses.

## Why this matters
- User impact: ...
- Business impact: ...

## Proposed scope
- Capability 1
- Capability 2
- Capability 3

## Success metrics
- Metric 1
- Metric 2

## Priority rationale
Rank: #N of 5
Reasoning: ...

## Codebase evidence
- Relevant files, modules, routes, or workflows reviewed.

## Risks / dependencies / open questions
- ...
```

## Final response

After creating the Linear projects, report:

- The 5 selected improvements in priority order
- The Linear project links/IDs that were created
- A short note on any assumptions made
- Any follow-up questions that would improve prioritization

Do not present more than 5 final projects unless the user explicitly asks for more.
