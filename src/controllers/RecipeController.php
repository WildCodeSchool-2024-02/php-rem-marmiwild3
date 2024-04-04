<?php
require_once __DIR__ . '/../models/RecipeModel.php';

class RecipeController
{
    private $model;

    public function __construct()
    {
        $this->model = new RecipeModel();
    }

    public function browse(): void
    {
        // Fetching all recipes

        $recipes = $this->model->getAll();

        // Generate the web page
        require __DIR__ . '/../views/indexRecipe.php';
    }

    public function show(int $id): void
    {
        // Input parameter validation (integer >0)
        $id = filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if (false === $id || null === $id) {
            die("Wrong input parameter");
        }

        // Fetching a recipe
        $recipe = $this->model->getById($id);

        // Result check
        if (!isset($recipe['title']) || !isset($recipe['description'])) {
            header("HTTP/1.1 404 Not Found");
            die("Recipe not found");
        }

        // Generate the web page
        require __DIR__ . '/../views/showRecipe.php';
    }

    public function add(): void
    {
        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $recipe = array_map('trim', $_POST);

            // Validate data
            $errors = $this->validateRecipe($recipe);

            // Save the recipe
            if (empty($errors)) {
                $this->model->save($recipe);
                header('Location: /');
            }
        }

        // Generate the web page
        require __DIR__ . '/../views/form.php';
    }

    public function edit(int $id): void
    {
        $errors = [];
        $recipe = $this->model->getById($id);
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $recipe = array_map('trim', $_POST);

            // Validate data
            $errors = $this->validateRecipe($recipe);

            // Save the recipe
            if (empty($errors)) {
                $this->model->update($recipe);
                header('Location: /');
            }
        }
        require __DIR__ . '/../views/form.php';
    }

    public function delete(int $id): void
    {
        $this->model->delete($id);
        header('Location: /');
    }

    public function validateRecipe(array $recipe): array
    {
        if (empty($recipe['title'])) {
            $errors[] = 'The title is required';
        }
        if (empty($recipe['description'])) {
            $errors[] = 'The description is required';
        }
        if (!empty($recipe['title']) && strlen($recipe['title']) > 255) {
            $errors[] = 'The title should be less than 255 characters';
        }

        return $errors ?? [];
    }
}
