<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Storage as FirebaseStorage;

class FirebaseController extends Controller
{
    protected $firebase;
    public function firebaseStorage($path = "")
    {
        return (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            // ->withDefaultStorageBucket(env('FIREBASE_STORAGE_BUCKET'))
            ->createStorage();
            // ->createStorage("gs://".env('FIREBASE_STORAGE_BUCKET').$path);
    }

    public function index()
    {
        $files = [];

        try {
            $bucket = $this->firebaseStorage()->getBucket();
            $objects = $bucket->objects(['orderBy' => 'Created', 'maxResults' => 2, 'prefix' => 'images/']);

            foreach ($objects as $object) {
                if ($object->name() === 'images/') {
                    continue;
                }

                $basename = basename($object->name());

                $files[] = [
                    'name' => $basename,
                    'url' => $object->signedUrl(new \DateTime('tomorrow')),
                    'metadata' => $object->info(),
                    'fullPath' => $object->name(),
                ];
            }
        } catch (FirebaseException $e) {
            dd('Error fetching data: ', $e->getMessage());
        }

        // dd($files);

        return view('firebase.upload', compact('files'));
    }

    // upload
    public function upload(Request $request)
    {
        // 10MB / images
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $type = $file->getClientMimeType();

            // set filename
            $extension = $request->file('file')->getClientOriginalExtension();
            $time = time();
            $random = rand(1000, 9999);
            $filename = uniqid() .'_'. $time . '_'.$random.'.' . $extension;

            // upload file
            $date = date('Y-m');
            $firebaseStoragePath = "images/$date";
            $bucket = $this->firebaseStorage()->getBucket();
            $object = $bucket->object($firebaseStoragePath .'/'.$filename);

            // $fileUpload = fopen($file, 'r');
            $fileUpload = file_get_contents($file->getRealPath());
            $bucket->upload($fileUpload, [
                'name' =>  $firebaseStoragePath .'/'. $filename,

                // save to path : images
                'predefinedAcl' => 'publicRead',

                // set metadata
                'metadata' => [
                    'contentType' => $type,
                    'cacheControl' => 'public, max-age=31536000',
                ],
            ]);

            // $url = $data->signedUrl(new \DateTime('tomorrow'));
            // $downloadUrl = $data->signedUrl();
            // save path to database

            return redirect()->route('index');
        } catch (FirebaseException $e) {
            dd('Error fetching data: ', $e->getMessage());
        }
    }

    public function deleteFile(Request $request){
        $fileName = $request->fileName ?? '';

        try {
            $bucket = $this->firebaseStorage()->getBucket();
            $object = $bucket->object($fileName);
            $object->delete();
            return redirect()->route('index');
        } catch (FirebaseException $e) {
            dd('Error fetching data: ', $e->getMessage());
        }
    }
}
