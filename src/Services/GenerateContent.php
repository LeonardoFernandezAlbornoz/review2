<?php

namespace App\Services;


class GenerateContent
{

    private $categories = ["Category 1", "Category 2", "Category 3"];
    private $notes = [

        [
            "description" => "Note 1",
            "category" => 1
        ],
        [
            "description" => "Note 2",
            "category" => 3
        ],
        [
            "description" => "Note 3",
            "category" => 2
        ],
        [
            "description" => "Note 4",
            "category" => 1
        ],
        [
            "description" => "Note 5",
            "category" => 1
        ]
    ];


    function getCategories()
    {
        return $this->categories;
    }

    function getNotes()
    {
        return $this->notes;
    }


}