<?php

function search_github_repositories($query) {
    $url = "https://api.github.com/search/repositories";
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

    if ($response !== false) {
        $data = json_decode($response, true);
        $repositories = $data['items'];
        return $repositories;
    } else {
        echo "Під час отримання репозиторіїв сталася помилка.";
        return array();
    }
}

function display_repository_info($repository) {
    echo "Назва репозиторію: " . $repository['full_name'] . "\n";
    echo "Опис: " . $repository['description'] . "\n";
    echo "Посилання: " . $repository['html_url'] . "\n";
    echo "Зірки: " . $repository['stargazers_count'] . "\n";
    echo "Форки: " . $repository['forks_count'] . "\n";
    echo "Спостерігачі: " . $repository['watchers_count'] . "\n";
    echo "Мова: " . $repository['language'] . "\n\n";
}

echo "Введіть пошуковий запит: ";
$search_query = trim(fgets(STDIN));

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

?>
