<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Blog;
use App\Role;
use App\User;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(Request $request){
       
        if (!Auth::check() && $request->path() != 'login') {
            return redirect('/login');
        }
        if (!Auth::check() && $request->path() == 'login') {
            return view('welcome');
        }
        $user = Auth::user();
        if ($user->role->isAdmin == 0) {
            return redirect('/login');
        }
        
        if ($request->path() == 'login') {
            return redirect('/');
        }
        return $this->checkForPermission($user,$request);
        
       

        //return $request->path();
    }

    public function checkForPermission($user,$request){
         $permission = json_decode($user->role->permission);
         if(!$permission) return view('welcome');
         $hasPermission = false;
         foreach($permission as $p){
            if ($p->name == $request->path()) {
                if($p->read){
                    $hasPermission = true;
                }
            }
         }
         if($hasPermission)  return view('welcome');
         return view('notfound');
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }

    public function adminLogin(Request $request){
        //validate
        $this->validate($request,[
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6',
        ]);

        if (Auth::attempt(['email'=>$request->email,'password'=>$request->password])) {
            $user = Auth::user();
            if($user->role->isAdmin == 0){
                Auth::logout();
                return response()->json([
                    'msg' => 'Incorrect login details'
                ],401);
            }
            return response()->json([
                'msg' => 'You are logged in',
                'user' => $user
            ]);
            
        }else{
            return response()->json([
                'msg' => 'Incorrect login details'
            ],401);
        }
    }


    public function getTags(){
        return Tag::orderBy('id','desc')->get();
    }
    public function addTag(Request $request){
        //validate
        $this->validate($request,[
            'tagName' => 'required',
        ]);

        return Tag::create([
            'tagName' => $request->tagName,
        ]);
    }
    public function editTag(Request $request){
        //validate
        $this->validate($request,[
            'id' => 'required',
            'tagName' => 'required',
        ]);

        return Tag::where('id',$request->id)->update([
            'tagName' => $request->tagName,
        ]);
    }
    public function deleteTag(Request $request){
        //validate
        $this->validate($request,[
            'id' => 'required',
        ]);

        return Tag::where('id',$request->id)->delete();
    }
    public function upload(Request $request){
        $this->validate($request,[
            'file' => 'required|mimes:jpeg,jpg,png'
        ]);
        $picName = time().'.'.$request->file->extension();
        $request->file->move(public_path('uploads'), $picName);
        return $picName;
    }

    public function uploadEditorImage(Request $request){
        $app_url = config('app.url');
        $this->validate($request,[
            'image' => 'required|mimes:jpeg,jpg,png'
        ]);
        $picName = time().'.'.$request->image->extension();
        $request->image->move(public_path('uploads'), $picName);
        return response()->json([
            "success" => 1,
            "file" => [
                "url" => "$app_url/uploads/$picName",
             ],
        ]);
    }

    public function deleteImage(Request $request){
        $fileName = $request->imageName;
        $this->deleteFileFromServer($fileName);
        return 'Done';
    }
    public function deleteFileFromServer($fileName){
        $filePath = public_path().$fileName;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        return;
    }
    public function addCategory(Request $request){
        //validate
        $this->validate($request,[
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);

        return Category::create([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }
    public function getCategories(){
        return  Category::orderBy('id','desc')->get();
    }
    public function editCategory(Request $request){
        //validate
        $this->validate($request,[
            'id' => 'required',
            'categoryName' => 'required',
            'iconImage' => 'required',
        ]);

        return Category::where('id',$request->id)->update([
            'categoryName' => $request->categoryName,
            'iconImage' => $request->iconImage,
        ]);
    }

    public function deleteCategory(Request $request){
        //First delete the original file from the server
        $this->deleteFileFromServer($request->iconImage);
        //validate
        $this->validate($request,[
            'id' => 'required',
        ]);

        return Category::where('id',$request->id)->delete();
    }






    public function getUsers(){
        return User::orderBy('id','desc')->where('role_id','!=',4)->get();
    }

    public function createUser(Request $request){
        $this->validate($request,[
            'fullName' => 'required',
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'role_id' => 'required',
        ]);

        $password = bcrypt($request->password);

        $user = User::create([
            'fullName' => $request->fullName,
            'email' => $request->email,
            'password' => $password,
            'role_id' => $request->role_id,
        ]);

        return $user;
    }

    public function editUser(Request $request){
        $this->validate($request,[
            'fullName' => 'required',
            'email' => "bail|required|email|unique:users,email,$request->id",
            'password' => 'min:6',
            'role_id' => 'required',
        ]);

        $data = [
            'fullName' => $request->fullName,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->password) {
            $password = bcrypt($request->password);
            $data['password'] = $password;
        }


        $user = User::where('id',$request->id)->update($data);

        return $user;
    }







    public function addRole(Request $request){
        //validate
        $this->validate($request,[
            'roleName' => 'required',
        ]);

        return Role::create([
            'roleName' => $request->roleName,
        ]);
    }
    public function getRoles(){
        return Role::all();
    }
    public function editRole(Request $request){
        //validate
        $this->validate($request,[
            'id' => 'required',
            'roleName' => 'required',
        ]);

        return Role::where('id',$request->id)->update([
            'roleName' => $request->roleName,
        ]);
    }

    public function assignRoles(Request $request){
         //validate
         $this->validate($request,[
            'role_id' => 'required',
            'permission' => 'required',
        ]);

        return Role::where('id',$request->role_id)->update([
            'permission' => $request->permission,
        ]);
    }

    // public function slug(){
    //     $title = 'This is a title new';
    //     return Blog::create([
    //         'title' => $title,
    //         'post' => 'This is a Post',
    //         'post_excerpt' => 'post excerpt',
    //         'user_id' => 1,
    //         'metaDescription' => 'meta description',
    //     ]);
    //     return $title;        
    // }

    public function createBlog(Request $request){
 
        return Blog::create([
            'title' => $request->title,
            'post' => $request->post,
            'post_excerpt' => $request->post_excerpt,
            'user_id' => Auth::user()->id,
            'metaDescription' => $request->metaDescription,
            'jsonData' => $request->jsonData,
        ]);       
    }

}

