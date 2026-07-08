# Local Automation Service Setup

## Purpose

This guide explains how to configure project-side internal automation services for the local Instagram pilot.

## Like Service

- Admin path: `Services`
- Mode: `manual`
- Service Type: `Default`
- Suggested Name: `IG Likes (Local)`
- Suggested Description: `Internal automated likes via private accounts`
- Suggested Tags: `male,female,music,fashion,all`
- Valid target: Instagram post, reel, or video URL

## Follow Service

- Admin path: `Services`
- Mode: `manual`
- Service Type: `Default`
- Suggested Name: `IG Follow (Local)`
- Suggested Description: `Internal automated follows via private accounts`
- Suggested Tags: `male,female,music,fashion,all,action:follow`
- Valid target: Instagram profile URL

## Important Rule

- The project detects `follow` automation when the service name, description, or tags include:
  - `follow`
  - `followers`
  - `action:follow`
- If none of those markers exist, the project treats the service as a `like` automation service.

## Pilot Recommendation

- Start with quantity `1`
- Test one like order first
- Test one follow order second
- Test one `organic` order third
- Stop immediately if you see:
  - `login_required`
  - repeated `302`
  - repeated `404`
  - blocked/checkpointed accounts
