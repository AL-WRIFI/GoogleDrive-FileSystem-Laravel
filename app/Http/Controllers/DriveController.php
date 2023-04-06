<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Models\User;

class DriveController extends Controller
{
    public $gClient;
    function __construct(){
        
        $this->gClient = new \Google_Client();
        
        $this->gClient->setApplicationName('Web client 1'); // ADD YOUR AUTH2 APPLICATION NAME (WHEN YOUR GENERATE SECRATE KEY)
        $this->gClient->setClientId('374817685459-k27v336f1ucn9h13u937puora5vce0n0.apps.googleusercontent.com');
        $this->gClient->setClientSecret('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');
        $this->gClient->setRedirectUri(route('index.user'));
        $this->gClient->setDeveloperKey('AIzaSyBTRl5UeBlIOtsqz6HfpjxIXfDq1f85qjM');
        $this->gClient->setScopes(array(               
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/drive'
            
        ));
        
        $this->gClient->setAccessType("offline");
        $this->gClient->setApprovalPrompt("force");
    }
    
    public function showPicker(Request $request)  {
        
        $google_oauthV2 = new \Google_Service_Oauth2($this->gClient);

        if ($request->get('code')){

            $this->gClient->authenticate($request->get('code'));
            $request->session()->put('token', $this->gClient->getAccessToken());
        }

        if ($request->session()->get('token')){

            $this->gClient->setAccessToken($request->session()->get('token'));
        }

        if ($this->gClient->getAccessToken()){

            //FOR LOGGED IN USER, GET DETAILS FROM GOOGLE USING ACCES
            $user = User::find(1);

            $user->access_token = json_encode($request->session()->get('token'));

            $user->save();       

            return view('index', ['token' => $request->session()->get('token')]);
            //dd("Successfully authenticated");
        
        } else{
            
            // FOR GUEST USER, GET GOOGLE LOGIN URL
            $authUrl = $this->gClient->createAuthUrl();

            return redirect()->to($authUrl);
        }
    }
// public function showPicker()
// {
//     $client = new GoogleClient();
//     $client->setAuthConfig(public_path('client_secret.json'));
//     // $client->setAuthConfig('/path/to/client_secret.json');
//     $client->setRedirectUri(url('/index'));
//     $client->addScope('https://www.googleapis.com/auth/drive.file');

//     // if (!isset($_GET['code'])) {
//     //     $authUrl = $client->createAuthUrl();
//     //     return view('welcome', ['authUrl' => $authUrl]);
//     // } else {
//         $client->fetchAccessTokenWithAuthCode($_GET['code']);
//         $token = $client->getAccessToken();
//         dd($token);
//         return view('index', ['token' => $token]);
//     // }
   
// }

public function pickFile(Request $request)
{
    dd('jhbvh');
    $client = new GoogleClient();
    $client->setAccessToken($request->token);
    $service = new Google_Service_Drive($client);

    $file = $service->files->get($request->fileId);
    $content = $service->files->get($request->fileId, ['alt' => 'media'])->getBody()->getContents();

    Storage::disk('local')->put($file->getName(), $content);

    return response()->json(['success' => 'File saved successfully.']);
}


}
