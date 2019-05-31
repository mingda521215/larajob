<?php

namespace App\Http\Controllers;

use App\Company;
use App\Job;
use Illuminate\Http\Request;
use App\Http\Requests\JobPostRequest;
use Auth;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('employer', ['except' => array('index', 'show', 'apply')]);
    }
    public function index()
    {
        $jobs = Job::all()->take(10);  // display 10 records
        return view('welcome', compact('jobs'));
    }

    public function show($id, Job $job)
    {
        return view('jobs.show', compact('job'));
    }

    public function company()
    {
        return view('company.index');
    }

    public function myjob()
    {
        $jobs = Job::where('user_id', auth()->user()->id)->get();
        return view('jobs.myjob', compact('jobs'));
    }

    public function edit($id)
    {
        $job = Job::findOrFail($id);
        return view('jobs.edit', compact('job'));
    }

    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $job->update($request->all());
        return redirect()->back()->with('message', 'Job successfully updated!');
    }

    public function create()
    {
        return view('jobs.create');
    }

    public function store(JobPostRequest $request)
    {
        $user_id = auth()->user()->id;
        $company = Company::where('user_id', $user_id)->first();
        $company_id = $company->id;
        Job::create([
            'user_id' => $user_id,
            'company_id' => $company_id,
            'title' => request('title'),
            'slug' => str_slug(request('title')),
            'description' => request('description'),
            'roles' => request('roles'),
            'category_id' => request('category'),
            'position' => request('position'),
            'address' => request('address'),
            'type' => request('type'),
            'status' => request('status'),
            'last_date' => request('last_date')
        ]);
        return redirect()->back()->with('message', 'Job posted successfuly!');
    }

    public function apply(Request $request, $id)
    {
        $jobId = Job::find($id);
        $jobId->users()->attach(Auth::user()->id);
        return redirect()->back()->with('message', 'Application sent!');
    }
}
