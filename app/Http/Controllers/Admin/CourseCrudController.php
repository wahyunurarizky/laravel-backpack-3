<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CourseRequest;
use App\Models\Student;
use App\Models\Teacher;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Redis;

/**
 * Class CourseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CourseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation {
        destroy as traitDestroy;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation {
        show as backpackView;
    }


    public function store()
    {

        $response = $this->traitStore();

        Redis::publish('updated_data', json_encode(['course', 'student', 'teacher']));

        return $response;
    }

    public function update()
    {
        $response = $this->traitUpdate();
        Redis::publish('updated_data', 'student');
        Redis::publish('updated_data', 'course');
        Redis::publish('updated_data', 'teacher');
        // do something after save
        return $response;
    }

    public function destroy($id)
    {
        $this->crud->hasAccessOrFail('delete');

        Redis::publish('updated_data', 'student');
        Redis::publish('updated_data', 'course');
        Redis::publish('updated_data', 'teacher');

        return $this->crud->delete($id);
    }

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Course::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/course');
        CRUD::setEntityNameStrings('course', 'courses');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->setFromDb();
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CourseRequest::class);
        // $this->crud->setFromDb();

        $this->crud->addField([
            'name'  => 'name',
            'label' => 'name',
            'type'  => 'text'
        ]);
        $this->crud->addField([
            'name'  => 'level',
            'label' => 'level',
            'type'  => 'enum'
        ]);
        $this->crud->addField([
            // n-n relationship (with pivot table)
            'label'     => 'Students', // Table column heading
            'type'      => 'select2_multiple',
            'name'      => 'students', // the method that defines the relationship in your Model
            'entity'    => 'students', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => Student::class, // foreign key model
            'pivot'     => true,
        ]);

        $this->crud->addField([
            // n-n relationship (with pivot table)
            'label'     => 'Teachers', // Table column heading
            'type'      => 'select2_multiple',
            'name'      => 'teachers', // the method that defines the relationship in your Model
            'entity'    => 'teachers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model'     => Teacher::class, // foreign key model
            'pivot'     => true,
            // 'select_all' => true
        ]);

        // $this->crud->addField([
        //     'label' => 'test',
        //     'type' => 'text',
        //     'name' => 'name'
        // ]);


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function show($id)
    {
        $view = $this->backpackView($id);

        $this->crud->addColumn([
            'label' => 'students',
            'name' => 'students',
            'type' => 'relationship'
        ]);

        $this->crud->addColumn([
            'label' => 'teachers',
            'name' => 'teachers',
            'type' => 'relationship'
        ]);

        return $view;
    }
}
