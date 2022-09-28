<?php

namespace App\Services;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Support\Facades\Storage;
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

    public function showImage($id)
    {
        $image = Storage::get('public/'.$this->findOne($id)->image);
        $mime = Storage::mimeType('public/'.$this->findOne($id)->image);
        return [$image, $mime];
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
    public function create(StoreStudentRequest $studentRequest)
    {
        if ($this->findOneByEmail($studentRequest->input('email')))
        {
            throw new ConflictHttpException('There is another user with the same email');
        }

        $student = new Student($studentRequest->except('image'));

        $file = $studentRequest -> file('image');
        if ($file)
        {
            $student -> image = $file -> store('images', 'public');
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
    public function update($id, UpdateStudentRequest $studentRequest)
    {
        $studentWithSameEmail = $this -> findOneByEmail($studentRequest->input('email'));
        if ($studentWithSameEmail && $studentWithSameEmail->id != $id)
        {
            throw new ConflictHttpException('There is another user with the same new email');
        }

        $student = $this -> findOne($id);

        $student -> update($studentRequest->except('image'));

        $file = $studentRequest -> file('image');
        if ($file)
        {
            if ($student->image != null)
                Storage::delete('public/'.$student->image);

            $student -> update(['image' => $file -> store('images', 'public')]);
        }

        return $this -> findOne($id);
    }

    /**
     * deletes a student
     * @param $id
     */
    public function delete($id)
    {
        $student = $this -> findOne($id);

        if ($student -> image != null)
            Storage::delete('public/'.$student->image);

        return $student->delete();
    }
}