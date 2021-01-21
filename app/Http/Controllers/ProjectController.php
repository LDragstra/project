<?php

namespace App\Http\Controllers;

use App\Factuur;
use App\Project;
use App\Uur;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
    {
        return Uur::orderBy('id', 'desc')->get()->groupBy('projectnr')->take(30);
    }

    public function show($id)
    {
        $id = strtok($id, '-');
        $project = Project::find($id);

        $inkopen = DB::table('inkoop')->where('projectid', $project->id)->get();

        $brutowinst = $project->berekenProjectGefactureerd($project->getProjectSoort(), Uur::project($project->id)->get());
        $inkoopBedrag = ($inkopen->sum('bedrag')) ? $inkopen->sum('bedrag') : 1;
        $percentageInkoop = $inkoopBedrag / $brutowinst['gefactureerd'] * 100;
        $projectFacturen = Factuur::allProjectInvoices($project->id)->where('id', '!=', $id)->orderBy('id', 'desc')->get();

        return view('pages.project', compact('project', 'inkopen', 'brutowinst', 'percentageInkoop', 'inkoopBedrag', 'projectFacturen'));
    }

}
