<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$BASE_URL = "https://cubingrf.org/competitions";
$CACHE_DIR = __DIR__ . "/data";
$CACHE_FILE = $CACHE_DIR . "/competitions.json";
$LOG_FILE = $CACHE_DIR . "/update.log";
$CACHE_EXPIRY = 3600;

$EVENT_MAP = [
    "event-222" => "222",
    "event-333" => "333",
    "event-444" => "444",
    "event-555" => "555",
    "event-666" => "666",
    "event-777" => "777",
    "event-333bf" => "333bf",
    "event-333oh" => "333oh",
    "event-333fm" => "333fm",
    "event-333ft" => "333ft",
    "event-pyram" => "pyram",
    "event-skewb" => "skewb",
    "event-minx" => "minx",
    "event-clock" => "clock",
    "event-sq1" => "sq1",
];

function generate_url($name) {
    global $BASE_URL;
    $cleaned = str_replace(["-", "&", "'"], "", $name);
    $cleaned = preg_replace('/\\s+/', '', $cleaned);
    return $BASE_URL . "/" . $cleaned;
}

function fetch_competitions() {
    global $BASE_URL, $EVENT_MAP;
    $html = file_get_contents($BASE_URL);
    if ($html === false) return [];
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    libxml_clear_errors();
    $xpath = new DOMXPath($doc);
    $names = $xpath->query('//div[contains(@class, "font-bold") and contains(@class, "text-lg")]');
    $competitions = [];
    foreach ($names as $name_tag) {
        $parent = $name_tag->parentNode;
        $name = trim($name_tag->nodeValue);
        $date_tag = $xpath->query('.//div[contains(@class, "text-gray-500")]', $parent);
        $date = $date_tag->length > 0 ? trim($date_tag->item(0)->nodeValue) : "";
        $city_tag = $xpath->query('.//div[contains(@class, "text-base")]', $parent);
        $city = $city_tag->length > 0 ? trim($city_tag->item(0)->nodeValue) : "";
        $discipline_tags = $xpath->query('.//i[contains(@class, "cubing-icon")]', $parent);
        $disciplines = [];
        foreach ($discipline_tags as $d) {
            foreach (explode(" ", $d->getAttribute("class")) as $cls) {
                if (strpos($cls, "event-") === 0) {
                    $disciplines[] = $EVENT_MAP[$cls] ?? $cls;
                    break;
                }
            }
        }
        $competitions[] = [
            "name" => $name,
            "date" => $date,
            "city" => $city,
            "url" => generate_url($name),
            "events" => $disciplines
        ];
    }
    return $competitions;
}

if (!is_dir($CACHE_DIR)) mkdir($CACHE_DIR, 0755, true);

$need_update = true;
if (file_exists($CACHE_FILE)) {
    $file_time = filemtime($CACHE_FILE);
    if ((time() - $file_time) < $CACHE_EXPIRY) $need_update = false;
}

if ($need_update) {
    $competitions = fetch_competitions();
    $result = file_put_contents($CACHE_FILE, json_encode($competitions, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    if ($result !== false) {
        $log_entry = "[" . date("Y-m-d H:i:s") . "] Обновлено соревнований: " . count($competitions) . PHP_EOL;
        file_put_contents($LOG_FILE, $log_entry, FILE_APPEND);
    }
}

$data = json_decode(file_get_contents($CACHE_FILE), true);
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
