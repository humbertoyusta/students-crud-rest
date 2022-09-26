<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Student;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class StudentsService
{
    /**
     * @param $id
     * @return the student with given $id or null if it doesn't exists
     */
    public function findOne($id)
    {
        return Student::find($id);
    }

    /**
     * @param $email
     * @return the student with given $email or null if it doesn't exists
     */
    public function findOneByEmail($email)
    {
        return Student::where('email', $email)->first();
    }

    /**
     * @return a list of all students
     */
    public function findAll()
    {
        return Student::all();
    }

    /**
     * internal private function to validate a student before adding to the database
     * @param $studentDto -- with all the data of the student
     * @param $isImageRequired -- a bool set to true if image is required
     * @throws a validation errror if validation fails
     */
    private function validateRequired(Request $studentRequest)
    {
        return $studentRequest->validate([
            'firstname' => 'required|max:120',
            'lastname' => 'required|max:120',
            'email' => 'required|max:255',
            'address' => 'required',
            'score' => 'required|min:0|numeric',
        ]);
    }

    private function validateOptional(Request $studentRequest)
    {
        return $studentRequest->validate([
            'firstname' => 'nullable|max:120',
            'lastname' => 'nullable|max:120',
            'email' => 'nullable|max:255',
            'address' => 'nullable',
            'score' => 'nullable|min:0|numeric',
        ]);
    }

    /**
     * internal private function to store an image
     * @param studentDto -- with all the data of the student
     * @return the filenime after app/public/images
     */
    private function storeImage($file)
    {
        //$file= $studentCollection->file('image');
        $filename= date('YmdHi').$file->getClientOriginalName();
        $file-> move(public_path('images'), $filename);
        return $filename;
    }

    /**
     * @param $studentDto -- with all the data of the student
     * @return $student -- the student added to the database
     * @throws a validation errror if validation fails
     * @throws ConflictHttpException -- if there is another user with the same email
     */
    public function add(Request $studentRequest)
    {
        $validatedRequest = $this -> validateRequired($studentRequest);

        if ($this->findOneByEmail($studentRequest->input('email')))
        {
            throw new ConflictHttpException('There is another user with the same email');
        }

        $student = new Student($validatedRequest);

        if ($studentRequest -> input('image') != null)
        {
            $studentRequest -> validate(['image' => 'image|size:2048|mimes:jpeg,png,jpg,gif']);
            $student -> image = $this->storeImage($studentRequest->file('image'));
        }

        $student -> save();
        return $student;
    }

    /**
     * @param $id -- id of the student to edit
     * @param $studentDto -- with all the data of the student
     * @return $student -- the student added to the database
     * @throws a validation errror if validation fails
     * @throws ConflictHttpException -- if there is another user with the same email
     */
    public function edit($id, Request $studentRequest)
    {
        $validatedRequest = $this -> validateOptional($studentRequest);

        if($studentRequest->input('id') != null && $id != $studentRequest->input('id'))
            throw new BadRequestHttpException('id of the element in route does not match request body id');

        $studentWithSameEmail = $this->findOneByEmail($studentRequest->input('email'));
        if ($studentWithSameEmail && $studentWithSameEmail->id != $id)
        {
            throw new ConflictHttpException('There is another user with the same new email');
        }

        $student = $this->findOne($id);

        /*$student->update([
            'firstname' => $studentRequest->input('firstname'),
            'lastname' => $studentRequest->input('lastname'),
            'email' => $studentRequest->input('email'),
            'address' => $studentRequest->input('address'),
            'score' => $studentRequest->input('score'),
        ]);*/
        $student->update($validatedRequest);

        if ($studentRequest->input('image') != null)
        {
            if ($student->image != null)
                unlink(public_path().'/images/'.$student->image);

            $studentRequest -> validate(['image' => 'image|size:2048|mimes:jpeg,png,jpg,gif']);
            $student->update([
                'image' => $this->storeImage($studentRequest->file('image')),
            ]);
        }

        return $this->findOne($id);
    }

    /**
     * deletes a student
     * @param $id
     */
    public function delete($id)
    {
        $student = Student::find($id);

        if ($student -> image != null)
            unlink(public_path().'/images/'.$student->image);

        return $student->delete();
    }
}