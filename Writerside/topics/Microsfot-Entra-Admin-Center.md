# Configure an Application in Microsoft Entra

To get started with this plugin, you will first need to configure an application in Microsoft Entra admin center. This
will allow you to configure who and how people can connect to your site. 

1. Go to [Microsoft Entra](https://entra.microsoft.com)
2. Click to expand "Identity" in the sidebar
3. Click to expand "Applications"
4. Click on "App registrations" (You can star this to make it show under "Favorites")
5. Click on "+ New Registration" (or select an existing app if you are coming back)

![app_registrations.png](app_registrations.png)

## Register an application

In the next window you will be asked to register your application. 

1. Select a name for your application, e.g. "MODX Entra Login for Example.com"
2. Select the "Supported account types" based on your needs
   - Single tenant will only allow users within your Entra directory
   - Multitenant will allow from any Entra directory
   - Personal will allow personal Microsoft accounts e.g. (Outlook, Xbox)
   - Multitenant + Personal will allow any Microsoft account
3. Set the Redirect URI
   - Set the platform to "Web"
   - Set the URL to `{{ url }}/{{ assets path }}/components/entralogin/callback.php`, e.g. 
     `https://example.com/assets/components/entralogin/callback.php`
   - If you are using MODX Revolution 2.x use `callback.v2.php` instead

<note>
   If you realize later that you selected the wrong supported account types follow the instructions below under 
   <a href="#supported_account_types">"Changing Supported Account Types"</a>
</note>

## Register additional Redirect URI's (optional)

If you need to set this application up for multiple sites, or a site with multiple domains, you will need to register a 
Redirect URI for each domain. This can be done by clicking in the "Redirect URIs" of the app registration page.

![redirect_uri.png](redirect_uri.png)

Once in here, simply click "Add URI" below your existing Redirect URI(s)

![redirect_uri_add.png](redirect_uri_add.png)

When all URI's are added, select "Save" at the bottom.

## Create a Client Secret

The overview window will show you your Application (client) ID, which you will be used for the [MODX system setting](Configuring-MODX.md#api-settings)
`entralogin.client_id`. You will also need to create a corresponding Client Secret for this app registration. To do 
this click on "Certificates & Secrets"

![cert_and_secrets.png](cert_and_secrets.png)

Once in the "Certificates & Secrets" section:

1. Make sure you are on the "Client secrets" tab
2. Click "+ New client secret"
3. Add a description for where this secret will be used
4. Set an expiration date.

![client_secret_add.png](client_secret_add.png)

5. Once generated, click the copy button next to the value

![client_secret_copy.png](client_secret_copy.png)

Make sure to save this value for later, as it will be used for the [MODX system setting](Configuring-MODX.md#api-settings) `entralogin.client_secret`

## Add API Permissions

The final step is to set up the permissions allowed by the application. To do this click on "API Permissions"

![api_permissions.png](api_permissions.png)

Once in the "API Permissions" section:

1. Click "+ Add a permission"
2. Select "Microsoft Graph"
3. Select "Delegated permissions"
4. Use the search box or scroll to enable the following permissions if they aren't already selected
   - offline_access
   - openid
   - profile
   - User.Read
5. Once you have verified all of these are selected, click "Add permissions"

![api_permissions_add.png](api_permissions_add.png)

## Changing Supported Account Types (optional) {id=supported_account_types}

If you realize later that you selected the wrong supported account types you can change it by editing the "Manifest".
This can be done by clicking in the "Manifest" of the app registration page.

![manifest.png](manifest.png)

The manifest is a JSON file which describes your application. To edit this file:

1. Look for the line that says "signInAudience"
2. Set the value of that line to one of the following:
   - `"AzureADMyOrg"` - Single tenant
   - `"AzureADMultipleOrgs"` - Multitenant 
   - `"PersonalMicrosoftAccount"` - Personal Accounts
   - `"AzureADandPersonalMicrosoftAccount"` - Multitenant + Personal Accounts
3. Save the manifest

![manifest_update.png](manifest_update.png)

You can learn more here [Supported Account Types](https://learn.microsoft.com/en-us/entra/identity-platform/supported-accounts-validation)
