<!DOCTYPE html>
<html>
  <head>
    <title>Drive API Quickstart</title>
    <meta charset="utf-8" />
  </head>
  <body>
    <p>Drive API Quickstart</p>

    <!--Add buttons to initiate auth sequence and sign out-->
    <button id="authorize_button" onclick="handleAuthClick()">Authorize</button>
    <button id="signout_button" onclick="handleSignoutClick()">Sign Out</button>

    <pre id="content" style="white-space: pre-wrap;"></pre>
    {{-- <button onclick="showPicker()">Select File</button>
    <button onclick="pickerCallback()">Select File</button>

	<!-- Display the selected file name and ID -->
	<p>Selected File: <span id="filename"></span></p>
	<p>Selected File ID: <span id="fileid"></span></p>

    <script>
        let tokenClient;
        let accessToken = null;
        let pickerInited = false;
        let gisInited = false;
  
        // Use the API Loader script to load google.picker
        function onApiLoad() {
          gapi.load('picker', onPickerApiLoad);
        }
  
        function onPickerApiLoad() {
          pickerInited = true;
        }
  
        function gisLoaded() {
          // TODO(developer): Replace with your client ID and required scopes
          tokenClient = google.accounts.oauth2.initTokenClient({
            client_id: '374817685459-snfrg2b1fi31fv1l96gbng4qk5s67qeb.apps.googleusercontent.com',
            scope: 'https://www.googleapis.com/auth/drive.readonly',
            callback: '', // defined later
          });
          gisInited = true;
      }
      </script>
      <!-- Load the Google API Loader script. -->
      <script async defer src="https://apis.google.com/js/api.js" onload="onApiLoad()"></script>
      <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>
    
      <script>

            // Create and render a Google Picker object for selecting from Drive
    function createPicker() {
      const showPicker = () => {
        // TODO(developer): Replace with your API key
        const picker = new google.picker.PickerBuilder()
            .addView(google.picker.ViewId.DOCS)
            .setOAuthToken(accessToken)
            .setDeveloperKey('YOUR_API_KEY')
            .setCallback(pickerCallback)
            .build();
        picker.setVisible(true);
      }

      // Request an access token
      tokenClient.callback = async (response) => {
        if (response.error !== undefined) {
          throw (response);
        }
        accessToken = response.access_token;
        showPicker();
      };

      if (accessToken === null) {
        // Prompt the user to select a Google Account and ask for consent to share their data
        // when establishing a new session.
        tokenClient.requestAccessToken({prompt: 'consent'});
      } else {
        // Skip display of account chooser and consent dialog for an existing session.
        tokenClient.requestAccessToken({prompt: ''});
      }
    }
    



        // A simple callback implementation.
        function pickerCallback(data) {
      let url = 'nothing';
      if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
        let doc = data[google.picker.Response.DOCUMENTS][0];
        url = doc[google.picker.Document.URL];
      }
      let message = `You picked: ${url}`;
      document.getElementById('result').innerText = message;
    }
    

       let picker = new google.picker.PickerBuilder()
        .addViewGroup(
          new google.picker.ViewGroup(google.picker.ViewId.DOCS)
              .addView(google.picker.ViewId.DOCUMENTS)
              .addView(google.picker.ViewId.PRESENTATIONS))
        .setOAuthToken(oauthToken)
        .setDeveloperKey(developerKey)
        .setCallback(pickerCallback)
        .build();
    


      </script> --}}









{{--       
    <script type="text/javascript">
      /* exported gapiLoaded */
      /* exported gisLoaded */
      /* exported handleAuthClick */
      /* exported handleSignoutClick */

      // TODO(developer): Set to client ID and API key from the Developer Console
      const CLIENT_ID = '374817685459-snfrg2b1fi31fv1l96gbng4qk5s67qeb.apps.googleusercontent.com';
      const API_KEY = 'AIzaSyBTRl5UeBlIOtsqz6HfpjxIXfDq1f85qjM';

      // Discovery doc URL for APIs used by the quickstart
      const DISCOVERY_DOC = 'https://www.googleapis.com/discovery/v1/apis/drive/v3/rest';

      // Authorization scopes required by the API; multiple scopes can be
      // included, separated by spaces.
      const SCOPES = 'https://www.googleapis.com/auth/drive.metadata.readonly';

      let tokenClient;
      let gapiInited = false;
      let gisInited = false;

      document.getElementById('authorize_button').style.visibility = 'hidden';
      document.getElementById('signout_button').style.visibility = 'hidden';

      /**
       * Callback after api.js is loaded.
       */
      function gapiLoaded() {
        gapi.load('client', initializeGapiClient);
      }

      /**
       * Callback after the API client is loaded. Loads the
       * discovery doc to initialize the API.
       */
      async function initializeGapiClient() {
        await gapi.client.init({
          apiKey: API_KEY,
          discoveryDocs: [DISCOVERY_DOC],
        });
        gapiInited = true;
        maybeEnableButtons();
      }

      /**
       * Callback after Google Identity Services are loaded.
       */
      function gisLoaded() {
        tokenClient = google.accounts.oauth2.initTokenClient({
          client_id: CLIENT_ID,
          scope: SCOPES,
          callback: '', // defined later
        });
        gisInited = true;
        maybeEnableButtons();
      }

      /**
       * Enables user interaction after all libraries are loaded.
       */
      function maybeEnableButtons() {
        if (gapiInited && gisInited) {
          document.getElementById('authorize_button').style.visibility = 'visible';
        }
      }

      /**
       *  Sign in the user upon button click.
       */
      function handleAuthClick() {
        tokenClient.callback = async (resp) => {
          if (resp.error !== undefined) {
            throw (resp);
          }
          document.getElementById('signout_button').style.visibility = 'visible';
          document.getElementById('authorize_button').innerText = 'Refresh';
          await listFiles();
        };

        if (gapi.client.getToken() === null) {
          // Prompt the user to select a Google Account and ask for consent to share their data
          // when establishing a new session.
          tokenClient.requestAccessToken({prompt: 'consent'});
        } else {
          // Skip display of account chooser and consent dialog for an existing session.
          tokenClient.requestAccessToken({prompt: ''});
        }
      }

      /**
       *  Sign out the user upon button click.
       */
      function handleSignoutClick() {
        const token = gapi.client.getToken();
        if (token !== null) {
          google.accounts.oauth2.revoke(token.access_token);
          gapi.client.setToken('');
          document.getElementById('content').innerText = '';
          document.getElementById('authorize_button').innerText = 'Authorize';
          document.getElementById('signout_button').style.visibility = 'hidden';
        }
      }

      /**
       * Print metadata for first 10 files.
       */
      async function listFiles() {
        let response;
        try {
          response = await gapi.client.drive.files.list({
            'pageSize': 10,
            'fields': 'files(id, name)',
          });
        } catch (err) {
          document.getElementById('content').innerText = err.message;
          return;
        }
        const files = response.result.files;
        if (!files || files.length == 0) {
          document.getElementById('content').innerText = 'No files found.';
          return;
        }
        // Flatten to string to display
        const output = files.reduce(
            (str, file) => `${str}${file.name} (${file.id})\n`,
            'Files:\n');
        document.getElementById('content').innerText = output;
      }
    </script>
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script> --}}
  </body>
</html>