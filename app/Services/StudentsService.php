<?php

namespace App\Services;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
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
        return Student::findOrFail($id);
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
     * @param $studentDto -- with all the data of the student
     * @return $student -- the student added to the database
     * @throws a validation errror if validation fails
     * @throws ConflictHttpException -- if there is another user with the same email
     */
    public function add(StoreStudentRequest $studentRequest)
    {
        if ($this->findOneByEmail($studentRequest->input('email')))
        {
            throw new ConflictHttpException('There is another user with the same email');
        }

        $student = new Student($studentRequest->except('image'));

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
    public function edit($id, UpdateStudentRequest $studentRequest)
    {
        if($studentRequest->input('id') != null && $id != $studentRequest->input('id'))
            throw new BadRequestHttpException('id of the element in route does not match request body id');

        $studentWithSameEmail = $this->findOneByEmail($studentRequest->input('email'));
        if ($studentWithSameEmail && $studentWithSameEmail->id != $id)
        {
            throw new ConflictHttpException('There is another user with the same new email');
        }

        $student = $this->findOne($id);

        $student->update($studentRequest->except('image'));

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