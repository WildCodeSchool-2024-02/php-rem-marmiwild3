<?php

require_once __DIR__ . '/../models/recipe-model.php';
require_once __DIR__ . '/../models/RecipeModel.php';

function browseRecipes(): void
{
    // Fetching all recipes
    $model = new RecipeModel();
    $recipes = $model->getAll();

    // Generate the web page
    require __DIR__ . '/../views/indexRecipe.php';
}

function showRecipe(int $id): void
{
    // Input parameter validation (integer >0)
    $id = filter_var($id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    if (false === $id || null === $id) {
        die("Wrong input parameter");
    }

    // Fetching a recipe
    $model = new RecipeModel();
    $recipe = $model->getById($id);

    // Result check
    if (!isset($recipe['title']) || !isset($recipe['description'])) {
        header("HTTP/1.1 404 Not Found");
        die("Recipe not found");
    }

    // Generate the web page
    require __DIR__ . '/../views/showRecipe.php';
}

function addRecipe(): void
{
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $recipe = array_map('trim', $_POST);

        // Validate data
        $errors = validateRecipe($recipe);

        // Save the recipe
        if (empty($errors)) {
            saveRecipe($recipe);
            header('Location: /');
        }
    }

    // Generate the web page
    require __DIR__ . '/../views/form.php';
}

function validateRecipe(array $recipe): array
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
