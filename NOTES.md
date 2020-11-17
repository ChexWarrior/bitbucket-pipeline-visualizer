## Goal
To be able to visualize multiple bitbucket pipelines at a time

### Stretch Goals
- Be able to start and stop individual bitbucket pipelines
- Be able to show history of pipelines

## Freewrite
Webpage shows form that allows user to enter a Bitbucket workspace and then it displays all current pipelines occuring in any repo within that workspace. Pipelines themselves display in their own containers showing each stage of pipeline and indicates if each stage has succeeded or failed.  When a new pipeline starts for a particular repo it replaces the current one (if any).

## Authentication with Bitbucket API
Don't want to use a db on backend for storing tokens so I think we'll have user enter username and bitbucket password (could be app password) and just not store at all.

## Process

### Credentials and Repository Fetch
#### Auth Succeeds
- User enters credentials
- Backend queries for all workspace repos with pipelines active
- Populates Repositories field
- User submits repositories
- Backend queries those repos for an active pipeline and creates boxes for each
#### Auth Fails
- Use enters credentials
- Display error message

### Display Pipelines
- For each pipeline determine if a pipeline is currently running
- Build HTML for each pipeline with differences based on current pipeline status:
  - No Pipeline Running (Show last status)
  - Pipeline Running
  

