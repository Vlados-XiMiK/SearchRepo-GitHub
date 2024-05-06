<?php

function search_github_repositories($query) {
    // Використовуйте змінну середовища або константу замість вставлення рядка напряму
    $url = getenv('GITHUB_API_URL') ?: "https://api.github.com/search/repositories";

    $params = array('q' => $query);
    $headers = array(
        'Accept: application/vnd.github.v3+json',
        'User-Agent: PHP'
    );
    $options = array(
        'http' => array(
            'header' => implode("\r\n", $headers),
            'method' => 'GET'
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents("$url?" . http_build_query($params), false, $context);

    if (!$response) {
        // Кинути виняток з помилкою
        throw new Exception("Під час отримання репозиторіїв сталася помилка.");
    }

    // Використовуйте !$response замість $response !== false для кращої оптимізації простору
    $data = json_decode($response, true);
    $repositories = $data['items'];
    return $repositories;
}

function display_repository_info($repository) {
    echo "Назва репозиторію: " . $repository['full_name'] . "\n";
    echo "Опис: " . $repository['description'] . "\n";
    echo "Посилання: " . $repository['html_url'] . "\n";
    echo "Зірки: " . $repository['stargazers_count'] . "\n";
    echo "Форки: " . $repository['forks_count'] . "\n";
    echo "Спостерігачі: " . $repository['watchers_count'] . "\n";

    // Виправлення виведення мови, яка може бути декількох
    echo "Мова: " . implode(', ', (array)$repository['language']) . "\n\n";
}

echo "Введіть пошуковий запит: ";
$search_query = trim(fgets(STDIN));

try {
    $repositories = search_github_repositories($search_query);

    if (!empty($repositories)) {
        echo "Результати пошуку:\n";
        foreach ($repositories as $key => $repo) {
            echo ($key + 1) . ". " . $repo['full_name'] . "\n";
        }

        echo "Введіть номер репозиторію, щоб переглянути деталі (або введіть 0 для виходу): ";
        $choice = trim(fgets(STDIN));

        if (is_numeric($choice) && $choice > 0 && $choice <= count($repositories)) {
            display_repository_info($repositories[$choice - 1]);
        } elseif ($choice == 0) {
            echo "Вихід з програми.\n";
        } else {
            echo "Недійсний вибір. Вихід з програми.\n";
        }
    } else {
        echo "Репозиторії не знайдені.\n";
    }
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}

?>
