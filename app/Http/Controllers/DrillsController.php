<?php

namespace App\Http\Controllers;

use App\Drill;
use App\Problem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DrillsController extends Controller
{

   

    public function index() {

        $drills = Problem::paginate(5);
        return view('drills.index', compact('drills'));
    }

    public function new() {


        return view('drills.new');
    }

    public function create(Request $request) {

        $request->validate([

            'title' => 'required|string|max:255',
            'category_name' => 'required|string|max:255',
            'problem0' => 'required|string|max:255',
            'problem1' => 'required|string|max:255',
            'problem2' => 'required|string|max:255',
            'problem3' => 'required|string|max:255',
            'problem4' => 'required|string|max:255',
            'problem5' => 'required|string|max:255',
            'problem6' => 'required|string|max:255',
            'problem7' => 'required|string|max:255',
            'problem8' => 'required|string|max:255',
            'problem9' => 'required|string|max:255'

        ]);

        $problem = new Problem; 

        Auth::user()->problems()->save($problem->fill($request->all()));

        return redirect('/drills')->with('flash_message', __('Registered'));
    }


    public function edit($id) {

        if(!ctype_digit($id)) {

            return redirect('/drills/new')->with('flash_message', __('Invalid operation was performed'));

           
        }

        $drill = Auth::user()->problems()->find($id);
        return view('drills.edit', compact('drill'));
    }

    public function update(Request $request, $id) {

        $drill = Drill::find($id);

        $drill->fill($request->all())->save();

        return redirect('drills/')->with('flash_message', __('Updated'));
    }

    public function destroy($id) {

        Auth::user()->drills()->find($id)->delete();

        return redirect('drills/')->with('flash_message', __('Deleted'));

    }

    public function show($id) {

        if(!ctype_digit($id)) {
            return redirect('/drills/')->with('flash_message', __('Invalid operation was performed'));
        }

        $drill = Problem::find($id);

        return view('drills.show', compact('drill'));

    }

    public function mypage() {
        $drills = Auth::user()->problems()->get();

        return view('drills.mypage', compact('drills'));
    }
 }
