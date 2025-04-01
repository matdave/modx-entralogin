# Configuring MODX

## System Settings

To set up Entra Login for MODX, you will need to go to your manager's system settings. 

Below are the settings and what they do. 

### API Settings

All API settings are **required** for Entra Login to function.

| Key                      | Description                                                    | Default                   |
|--------------------------|----------------------------------------------------------------|---------------------------|
| entralogin.auth_host     | Authentication domain for login.                               | login.microsoftonline.com |
| entralogin.graph_host    | Microsoft Graph domain for receiving user information.         | graph.microsoft.com       |
| entralogin.tenant_id     | ID of the Microsoft Entra directory tenant.                    | common                    |
| entralogin.client_id     | Client ID for from your Entra Application Dashboard            |                           |
| entralogin.client_secret | Client Secret for Oauth2 from your Entra Application Dashboard |                           |

### Security

| Key                              | Description                                                                                                                   | Default |
|----------------------------------|-------------------------------------------------------------------------------------------------------------------------------|---------|
| entralogin.allow_match_by_email  | Allow matching existing users by email address.                                                                               | Yes     |
| entralogin.allow_signup          | Allow new managers to signup with Entra. (Warning: this will allow anyone with a Entra account to sign up. Use with caution.) | No      |
| entralogin.allow_signup_active   | Default new signups to "active" users                                                                                         | No      |
| entralogin.allow_signup_domains  | Domain restrictions for new signups. Comma separated list of domains.                                                         |         |
| entralogin.allow_signup_notify   | Emails to notify of new signups. Comma separated list of emails.                                                              |         |
| entralogin.default_group         | Default MODX user group for new signups.                                                                                      |         |
| entralogin.default_role          | Default user group role for new signups.                                                                                      | Member  |
| entralogin.disable_regular_login | Disable the regular login form options. (Warning: this will disable the regular login form for all users.)                    | No      |


<warning>
    If entralogin.allow_signup is set to "Yes" anyone who matches your apps' Supported Account Types can create a MODX 
    account. It is highly recommend to limit the signups by setting restrictions on entralogin.allow_signup_domains or 
    setting entralogin.allow_signup_active to "No"
</warning>