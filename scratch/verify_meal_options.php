<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Restaurants\Models\Meal;
use Modules\Restaurants\Models\MealOption;
use Modules\Restaurants\Http\Resources\MealResource;

echo "--- MEAL OPTIONS INTEGRATION VERIFICATION ---\n";

try {
    // 1. Fetch first meal
    $meal = Meal::first();
    if (!$meal) {
        echo "No meal found in database.\n";
        exit(1);
    }
    echo "Found Meal ID: {$meal->id} - Name: {$meal->name}\n";

    // 2. Create a test option
    $testOption = MealOption::create([
        'meal_id' => $meal->id,
        'option_name' => 'Extra Cheese Test',
        'additional_price' => 1.50,
    ]);
    echo "Successfully created MealOption ID: {$testOption->id}\n";
    echo "Accessing aliases -> Name: {$testOption->name}, Price: {$testOption->price}\n";

    // 3. Test relationship from Meal -> Options
    $meal->load('options');
    $optionsCount = $meal->options->count();
    echo "Meal options count after load: {$optionsCount}\n";

    // 4. Test relationship from Option -> Meal
    $parentMeal = $testOption->meal;
    echo "Option parent meal ID: {$parentMeal->id} ({$parentMeal->name})\n";

    // 5. Test MealResource format
    $resource = (new MealResource($meal))->toArray(request());
    echo "MealResource output contains 'options': " . (isset($resource['options']) ? 'YES' : 'NO') . "\n";
    if (isset($resource['options'])) {
        $optionsArray = json_decode(json_encode($resource['options']), true);
        echo "Options in MealResource array: " . count($optionsArray) . "\n";
        if (count($optionsArray) > 0) {
            echo "First option sample in resource: " . json_encode($optionsArray[0]) . "\n";
        }
    }

    // 6. Clean up test option
    $testOption->delete();
    echo "Successfully deleted test MealOption ID: {$testOption->id}\n";
    echo "--- VERIFICATION PASSED SUCCESSFULLY ---\n";

} catch (\Throwable $e) {
    echo "VERIFICATION ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
