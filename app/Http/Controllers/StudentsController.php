<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Services\StudentsService;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StudentsController extends Controller
{
    private StudentsService $studentsService;

    /**
     * Constructor
     * Injecting studentsService
     */
    public function __construct(StudentsService $studentsService)
    {
        $this->studentsService = $studentsService;
    }

    /**
     * @return a list of students
     */
    public function findAll() 
    {
        return response()->json($this->studentsService->findAll(), Response::HTTP_OK);
    }

    /**
     * @return a view with student's data
     * @param id
     */
    public function findOne($id)
    {
        $student = $this->studentsService->findOne($id);
        if ($student)
            return response()->json($student, Response::HTTP_OK);
        else
            return response()->json(['message' => 'No student with id '.$id.' found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Adds a new student
     * @param $request - a request containing all data of a student
     */
    public function create(Request $request) 
    {
        return response()->json($this->studentsService->add($request), Response::HTTP_CREATED);
    }

    /**
     * Edits a student
     * @redirects to the same edit view with a success message or an error
     * @param $request - a request containing the new data of the student
     */
    public function update(Request $request, $id)
    {
        return response()->json($this->studentsService->edit($id, $request), Response::HTTP_OK);
    }

    /**
     * deletes a studeing given $id
     * @param id
     * @return a view of the list of students and a success message or error
     */
    public function delete($id)
    {
        $this->studentsService->delete($id);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
