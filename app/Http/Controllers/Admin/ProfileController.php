<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Profile;
use App\ProfileHistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function add()
    {
        return view('admin.profile.create');
    }

    public function create(Request $request)
    {
         // Validationをかける
        $this->validate($request, Profile::$rules);
        $profiles = new Profile;
        $form = $request->all();
        
        unset($form['_token']);
        
        $profiles->fill($form);
        $profiles->save();
        
        return redirect('admin/profile/create');
    }
    //課題17にて追記
    public function index(Request $request)
  {
      $cond_title = $request->cond_title;
      if ($cond_title != '') {
          $posts = Profile::where('title', $cond_title)->get();
      } else {
          $posts = Profile::all();
      }
      return view('admin.profile.index', ['posts' => $posts, 'cond_title' => $cond_title]);
  }
    

 public function edit(Request $request)
  {
      // profile Modelからデータを取得する
      $profile = Profile::find($request->id);
      if (empty($profile)) {
        abort(404);    
      }
      return view('admin.profile.edit', ['profile_form' => $profile]);
  }
    public function update(Request $request)
    {
         // Validationをかける
        $this->validate($request, Profile::$rules);
          //  Modelからデータを取得する
        $profiles = Profile::find($request->id);
        // 送信されてきたフォームデータを格納する
        $profiles_form = $request->all();
        
       if ($request->remove == 'true'){
          $profile_form['image_path'] = null;
      }elseif ($request->file('image')){
          $path = $request->file('image')->store('public/image');
          $profile_form['image_path'] = basename($path);
      }else{
          $profile_form['image_path'] = $profile->image_path;
      }
      
      unset($profile_form['_token']);
       unset($profile_form['image']);
      unset($profile_form['remove']);
      
      $profile->fill($profile_form)->save();
     
      $Profile_histories = new ProfileHistory;
      $Profile_histories->profile_id = $profile->id;
      $Profile_histories->edited_at = Carbon::now();
      $Profile_histories->save();

        
        return redirect('admin/profile/edit?id=' . $profile->id);
    }
    public function delete(Request $request)
  {
      // 該当するNews Modelを取得
      $profile = Profile::find($request->id);
      // 削除する
      $profile->delete();
      return redirect('admin/news/');
  }
}