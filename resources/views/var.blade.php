<!DOCTYPE html>
<html>
<head>
  <title>Drive</title>
  <meta charset="utf-8" />
</head>
<body>
<p>اختيار صورة GoogleDrive Api</p>

<!--Add buttons to initiate auth sequence and sign out-->
<button id="authorize_button" onclick="handleAuthClick()">تسجيل دخول</button>
<button id="signout_button" onclick="handleSignoutClick()"> تسجيل خروج</button>

<pre id="content" style="white-space: pre-wrap;"></pre>

<script type="text/javascript">

  /* exported gapiLoaded */
  /* exported gisLoaded */
  /* exported handleAuthClick */
  /* exported handleSignoutClick */

  // Authorization scopes required by the API; multiple scopes can be
  // included, separated by spaces.
  const SCOPES = 'https://www.googleapis.com/auth/drive.metadata.readonly';

  // TODO(developer): Set to client ID and API key from the Developer Console
  const CLIENT_ID = "374817685459-snfrg2b1fi31fv1l96gbng4qk5s67qeb.apps.googleusercontent.com";
  const API_KEY = 'AIzaSyBTRl5UeBlIOtsqz6HfpjxIXfDq1f85qjM';

  // TODO(developer): Replace with your own project number from console.developers.google.com.
  const APP_ID = '374817685459';

  let tokenClient;
  let accessToken = null;
  let pickerInited = false;
  let gisInited = false;


  document.getElementById('authorize_button').style.visibility = 'hidden';
  document.getElementById('signout_button').style.visibility = 'hidden';

  /**
   * Callback after api.js is loaded.
   */
  function gapiLoaded() {
    gapi.load('client:picker', intializePicker);
  }

  /**
   * Callback after the API client is loaded. Loads the
   * discovery doc to initialize the API.
  */
  async function intializePicker() {
    await gapi.client.load('https://www.googleapis.com/discovery/v1/apis/drive/v3/rest');
    pickerInited = true;
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
    if (pickerInited && gisInited) {
      document.getElementById('authorize_button').style.visibility = 'visible';
    }
  }

  /**
   *  Sign in the user upon button click.
   */
  function handleAuthClick() {
    tokenClient.callback = async (response) => {
      if (response.error !== undefined) {
        throw (response);
      }
      accessToken = response.access_token;
      document.getElementById('signout_button').style.visibility = 'visible';
      document.getElementById('authorize_button').innerText = 'اعادة اختيار';
      await createPicker();
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

  /**
   *  Sign out the user upon button click.
   */
  function handleSignoutClick() {
    if (accessToken) {
      accessToken = null;
      google.accounts.oauth2.revoke(accessToken);
      document.getElementById('content').innerText = '';
      document.getElementById('authorize_button').innerText = 'تسجيل دخول';
      document.getElementById('signout_button').style.visibility = 'hidden';
    }
  }

  /**
   *  Create and render a Picker object for searching images.
   */
  function createPicker() {
    const view = new google.picker.View(google.picker.ViewId.DOCS);
    // view.setMimeTypes('image/png,image/jpeg,image/jpg');
    const picker = new google.picker.PickerBuilder()
        // .enableFeature(google.picker.Feature.NAV_HIDDEN)
        .enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
        .setDeveloperKey(API_KEY)
        .setAppId(APP_ID)
        .setOAuthToken(accessToken)
        .addView(view)
        .addView(new google.picker.DocsUploadView())
        .setCallback(pickerCallback)
        .build();
    picker.setVisible(true);
  }

  /**
   * Displays the file details of the user's selection.
   * @param {object} data - Containers the user selection from the picker
   */
  async function pickerCallback(data) {
    if (data.action === google.picker.Action.PICKED) {
      let text = `Picker response: \n${JSON.stringify(data, null, 2)}\n`;
      const document = data[google.picker.Response.DOCUMENTS][0];
      const fileId = document[google.picker.Document.ID];
      const res = await gapi.client.drive.files.get({
          'fileId': fileId,
          'fields': '*',
      });

    const dat =  getFile(fileId)
    dat.then(function(result) {

    alert('تم اختيار صورة بنجاح');
      console.log(result) // "Some User token"
    })
  };
}

// gapi.load('client', function() {
//     // Authenticate the user using the client ID and client secret from your credentials file
//     gapi.auth.authorize({
//       'client_id': '374817685459-snfrg2b1fi31fv1l96gbng4qk5s67qeb.apps.googleusercontent.com',
//       'scope': ['https://www.googleapis.com/auth/drive.readonly']
//     }, function() {
//       // Initialize the file picker
//       var picker = new google.picker.PickerBuilder()
//         .addView(google.picker.ViewId.DOCS)
//         .setOAuthToken(gapi.auth.getToken().access_token)
//         .setDeveloperKey('AIzaSyBTRl5UeBlIOtsqz6HfpjxIXfDq1f85qjM')
//         .setCallback(function(data) {
//           if (data.action == google.picker.Action.PICKED) {
//             // Get the file ID of the selected file
//             var fileId = data.docs[0].id;
            
//             // Use the Google Drive API to download the file
//             var xhr = new XMLHttpRequest();
//             xhr.open('GET', 'https://www.googleapis.com/drive/v3/files/' + fileId + '?alt=media');
//             xhr.setRequestHeader('Authorization', 'Bearer ' + gapi.auth.getToken().access_token);
//             xhr.onload = function() {
//               // Store the downloaded file in Laravel's public directory
//               var formData = new FormData();
//               formData.append('file', xhr.response, data.docs[0].name);
//               $.ajax({
//                 url: '/upload',
//                 method: 'POST',
//                 data: formData,
//                 contentType: false,
//                 processData: false,
//                 success: function(response) {
//                   console.log(response);
//                 }
//               });
//             };
//             xhr.send();
//           }
//         });
//       picker.build().setVisible(true);
//     });
//   });
//Download the file to blob format
  async function getFile (fileId) {
    const URL = 'https://www.googleapis.com/drive/v3/files';
    const FIELDS = 'name, mimeType, modifiedTime';
    
    const { gapi: { auth, client: { drive: { files } } } } = window;
    const { access_token: accessToken } = auth.getToken();
    const fetchOptions = { headers: { Authorization: `Bearer ${accessToken}` } };
    
    const {
      result: { name, mimeType, modifiedTime }
    } = await files.get({ fileId, fields: FIELDS });
    const blob = await fetch(`${URL}/${fileId}?alt=media`, fetchOptions).then(res => res.blob());
    const fileOptions = {
      type: mimeType,
      lastModified: new Date(modifiedTime).getTime(),
    };
    
    return new File([blob], name, fileOptions);
};


</script>
<script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
<script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>
</body>
</html>