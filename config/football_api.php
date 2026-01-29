<?php

function loadEnvFile(string $path): void{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if (!$lines) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        if ($key === '') {
            continue;
        }
        if ((strlen($value) >= 2) && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

loadEnvFile(__DIR__ . '/../.env');

if (!defined('FOOTBALL_API_KEY')) {
    $envKey = getenv('FOOTBALL_API_KEY');
    define('FOOTBALL_API_KEY', $envKey !== false ? $envKey : '');
}
define('FOOTBALL_API_URL', 'https://api.football-data.org/v4');
define('FOOTBALL_API_HOST', 'api.football-data.org');
define('USE_RAPIDAPI', false); 


function getCachedResponse($cacheKey, $ttl = 300) { 
    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . '/' . md5($cacheKey) . '.json';
    
    if (file_exists($cacheFile)) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached && (time() - $cached['timestamp']) < $ttl) {
            return $cached['data'];
        }
    }
    
    return null;
}

function setCachedResponse($cacheKey, $data) {
    $cacheDir = __DIR__ . '/../cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    
    $cacheFile = $cacheDir . '/' . md5($cacheKey) . '.json';
    file_put_contents($cacheFile, json_encode([
        'timestamp' => time(),
        'data' => $data
    ]));
}

function makeFootballAPIRequest($endpoint, $params = [], $useCache = true, $cacheTTL = 300) {
    if (FOOTBALL_API_KEY === '' || FOOTBALL_API_KEY === 'your_rapidapi_key_here' || FOOTBALL_API_KEY === 'YOUR_FOOTBALL_DATA_TOKEN_HERE') {
        return [
            'success' => false,
            'error' => 'Football API key/token not configured. Set FOOTBALL_API_KEY in .env or your environment before running the app.'
        ];
    }
    
    $cacheKey = $endpoint . '?' . http_build_query($params);
    if ($useCache) {
        $cached = getCachedResponse($cacheKey, $cacheTTL);
        if ($cached !== null) {
            return [
                'success' => true,
                'data' => $cached,
                'cached' => true
            ];
        }
    }
    
    $url = FOOTBALL_API_URL . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    
    if (defined('USE_RAPIDAPI') && USE_RAPIDAPI) {
        $headers = [
            'X-RapidAPI-Key: ' . FOOTBALL_API_KEY,
            'X-RapidAPI-Host: ' . FOOTBALL_API_HOST
        ];
    } else {
        $headers = [
            'X-Auth-Token: ' . FOOTBALL_API_KEY,
            'Accept: application/json'
        ];
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $curlErrno = curl_errno($ch);
    curl_close($ch);
    
    if ($error) {
        $errorMessage = 'CURL Error: ' . $error;
        
        if ($curlErrno === CURLE_COULDNT_RESOLVE_HOST) {
            $errorMessage = 'Network Error: Could not resolve host "' . parse_url($url, PHP_URL_HOST) . '". ' .
                          'Please check your internet connection and DNS settings. ' .
                          'If you are offline, the system will use cached data when available.';
        } elseif ($curlErrno === CURLE_OPERATION_TIMEOUTED || $curlErrno === CURLE_COULDNT_CONNECT) {
            $errorMessage = 'Connection Timeout: Could not connect to the Football API. ' .
                          'Please check your internet connection. The system will use cached data when available.';
        }
        
        return [
            'success' => false,
            'error' => $errorMessage,
            'curl_errno' => $curlErrno,
            'offline' => true
        ];
    }
    
    if ($httpCode === 429) {
        $responseData = json_decode($response, true);
        $message = 'Rate limit exceeded. ';
        
        if (isset($responseData['message'])) {
            $message .= $responseData['message'];
        } else {
            $message .= 'Please wait a few minutes before making more requests. Free tier allows ~10 requests per minute.';
        }
        
        return [
            'success' => false,
            'error' => $message,
            'http_code' => 429,
            'rate_limited' => true,
            'response' => $response
        ];
    }
    
    if ($httpCode !== 200) {
        $responseData = json_decode($response, true);
        $errorMsg = 'API Error: HTTP ' . $httpCode;
        
        if (isset($responseData['message'])) {
            $errorMsg = $responseData['message'];
        }
        
        return [
            'success' => false,
            'error' => $errorMsg,
            'http_code' => $httpCode,
            'response' => $response
        ];
    }
    
    $data = json_decode($response, true);
    
    if ($useCache && $data !== null) {
        setCachedResponse($cacheKey, $data);
    }
    
    return [
        'success' => true,
        'data' => $data
    ];
}


function getUpcomingMatches($leagueId = 'PL', $days = 7) {
    
    $leagueMap = [
        39 => 'PL',
        140 => 'PD',    
        78 => 'BL1',  
        135 => 'SA', 
        61 => 'FL1'    
    ];
    
    $competitionCode = isset($leagueMap[$leagueId]) ? $leagueMap[$leagueId] : $leagueId;
    
    $result = makeFootballAPIRequest("/competitions/{$competitionCode}/matches", [
        'dateFrom' => date('Y-m-d'),
        'dateTo' => date('Y-m-d', strtotime("+$days days"))
    ], true, 600);
    
    if (!$result['success']) {
        return $result;
    }
    
    $matches = $result['data']['matches'] ?? [];
    $now = time();
    $transformedMatches = [];
    
    foreach ($matches as $match) {
        $matchStatus = $match['status'] ?? '';
        $matchTimestamp = strtotime($match['utcDate']);
        
        if (($matchStatus === 'TIMED' || $matchStatus === 'SCHEDULED') && 
            $matchTimestamp > $now &&
            $matchStatus !== 'CANCELLED' && 
            $matchStatus !== 'POSTPONED') {
            $transformedMatches[] = [
                'fixture' => [
                    'id' => $match['id'],
                    'timestamp' => $matchTimestamp,
                    'status' => ['short' => $matchStatus]
                ],
                'teams' => [
                    'home' => [
                        'id' => $match['homeTeam']['id'] ?? null,
                        'name' => $match['homeTeam']['name'],
                        'crest' => $match['homeTeam']['crest'] ?? null
                    ],
                    'away' => [
                        'id' => $match['awayTeam']['id'] ?? null,
                        'name' => $match['awayTeam']['name'],
                        'crest' => $match['awayTeam']['crest'] ?? null
                    ]
                ],
                'goals' => [
                    'home' => $match['score']['fullTime']['home'] ?? null,
                    'away' => $match['score']['fullTime']['away'] ?? null
                ]
            ];
        }
    }
    
    return [
        'success' => true,
        'matches' => $transformedMatches
    ];
}


function getFinishedMatches($leagueId = 'PL', $days = 7) {
    $leagueMap = [
        39 => 'PL', 140 => 'PD', 78 => 'BL1', 135 => 'SA', 61 => 'FL1'
    ];
    $competitionCode = isset($leagueMap[$leagueId]) ? $leagueMap[$leagueId] : $leagueId;
    
    $result = makeFootballAPIRequest("/competitions/{$competitionCode}/matches", [
        'dateFrom' => date('Y-m-d', strtotime("-$days days")),
        'dateTo' => date('Y-m-d')
    ]);
    
    if (!$result['success']) {
        return $result;
    }
    
    $matches = $result['data']['matches'] ?? [];
    $transformedMatches = [];
    foreach ($matches as $match) {
        $matchStatus = $match['status'] ?? '';
        if ($matchStatus === 'FINISHED') {
            $statistics = extractMatchStatistics($match);
            
            $transformedMatches[] = [
                'fixture' => [
                    'id' => $match['id'],
                    'timestamp' => strtotime($match['utcDate']),
                    'status' => ['short' => $matchStatus]
                ],
                'teams' => [
                    'home' => [
                        'id' => $match['homeTeam']['id'] ?? null,
                        'name' => $match['homeTeam']['name'],
                        'crest' => $match['homeTeam']['crest'] ?? null
                    ],
                    'away' => [
                        'id' => $match['awayTeam']['id'] ?? null,
                        'name' => $match['awayTeam']['name'],
                        'crest' => $match['awayTeam']['crest'] ?? null
                    ]
                ],
                'goals' => [
                    'home' => $match['score']['fullTime']['home'] ?? null,
                    'away' => $match['score']['fullTime']['away'] ?? null
                ],
                'goals_1h' => [
                    'home' => $match['score']['halfTime']['home'] ?? null,
                    'away' => $match['score']['halfTime']['away'] ?? null
                ],
                'statistics' => $statistics
            ];
        }
    }
    
    return [
        'success' => true,
        'matches' => $transformedMatches
    ];
}


function getLiveMatches($leagueId = 'PL') {
    $leagueMap = [
        39 => 'PL', 140 => 'PD', 78 => 'BL1', 135 => 'SA', 61 => 'FL1'
    ];
    $competitionCode = isset($leagueMap[$leagueId]) ? $leagueMap[$leagueId] : $leagueId;
    
    $result = makeFootballAPIRequest("/matches", [
        'status' => 'LIVE',
        'competitions' => $competitionCode,
        'limit' => 20
    ], true, 30);
    
    if (!$result['success'] || empty($result['data']['matches'] ?? [])) {
        $result = makeFootballAPIRequest("/competitions/{$competitionCode}/matches", [
            'dateFrom' => date('Y-m-d'),
            'dateTo' => date('Y-m-d')
        ], true, 30);
    }
    
    if (!$result['success']) {
        return $result;
    }
    
    $matches = $result['data']['matches'] ?? [];
    $transformedMatches = [];
    
    foreach ($matches as $match) {
        $matchStatus = $match['status'] ?? '';
        
        if (in_array($matchStatus, ['IN_PLAY', 'LIVE', '1H', '2H', 'HT', 'ET', 'PEN_LIVE', 'PAUSED']) ||
            stripos($matchStatus, 'LIVE') !== false || stripos($matchStatus, 'PLAY') !== false) {
            
            $homeScore = null;
            $awayScore = null;
            
            if (isset($match['score'])) {
                if (isset($match['score']['fullTime']['home'])) {
                    $homeScore = $match['score']['fullTime']['home'];
                    $awayScore = $match['score']['fullTime']['away'];
                } elseif (isset($match['score']['halfTime']['home'])) {
                    $homeScore = $match['score']['halfTime']['home'];
                    $awayScore = $match['score']['halfTime']['away'];
                } elseif (isset($match['score']['regularTime']['home'])) {
                    $homeScore = $match['score']['regularTime']['home'];
                    $awayScore = $match['score']['regularTime']['away'];
                }
            }
            
            $elapsed = $match['minute'] ?? (isset($match['score']['minute']) ? $match['score']['minute'] : null);
            
            $transformedMatches[] = [
                'fixture' => [
                    'id' => $match['id'],
                    'timestamp' => strtotime($match['utcDate']),
                    'status' => [
                        'short' => $matchStatus,
                        'elapsed' => $elapsed,
                        'long' => $match['status'] ?? $matchStatus
                    ]
                ],
                'teams' => [
                    'home' => [
                        'id' => $match['homeTeam']['id'] ?? null,
                        'name' => $match['homeTeam']['name'],
                        'crest' => $match['homeTeam']['crest'] ?? null
                    ],
                    'away' => [
                        'id' => $match['awayTeam']['id'] ?? null,
                        'name' => $match['awayTeam']['name'],
                        'crest' => $match['awayTeam']['crest'] ?? null
                    ]
                ],
                'goals' => [
                    'home' => $homeScore,
                    'away' => $awayScore
                ]
            ];
        }
    }
    
    return [
        'success' => true,
        'matches' => $transformedMatches
    ];
}


function getMatchById($fixtureId) {
    $result = makeFootballAPIRequest("/matches/{$fixtureId}", []);
    
    if (!$result['success']) {
        return $result;
    }
    
    $match = $result['data'] ?? null;
    
    if (!$match) {
        return [
            'success' => false,
            'error' => 'Match not found'
        ];
    }
    
    $statistics = extractMatchStatistics($match);
    
    $transformedMatch = [
        'fixture' => [
            'id' => $match['id'],
            'timestamp' => strtotime($match['utcDate']),
            'status' => ['short' => $match['status']]
        ],
        'teams' => [
            'home' => [
                'id' => $match['homeTeam']['id'] ?? null,
                'name' => $match['homeTeam']['name'],
                'crest' => $match['homeTeam']['crest'] ?? null
            ],
            'away' => [
                'id' => $match['awayTeam']['id'] ?? null,
                'name' => $match['awayTeam']['name'],
                'crest' => $match['awayTeam']['crest'] ?? null
            ]
        ],
        'goals' => [
            'home' => $match['score']['fullTime']['home'] ?? null,
            'away' => $match['score']['fullTime']['away'] ?? null
        ],
        'goals_1h' => [
            'home' => $match['score']['halfTime']['home'] ?? null,
            'away' => $match['score']['halfTime']['away'] ?? null
        ],
        'statistics' => $statistics
    ];
    
    return [
        'success' => true,
        'match' => $transformedMatch
    ];
}


function extractMatchStatistics($match) {
    
    if ($match['status'] === 'FINISHED') {
        return generateMockStatistics($match);
    }
    
    
    return [
        'corners' => ['home' => null, 'away' => null],
        'corners_1h' => ['home' => null, 'away' => null],
        'yellow_cards' => ['home' => null, 'away' => null],
        'yellow_cards_1h' => ['home' => null, 'away' => null],
        'red_cards' => ['home' => null, 'away' => null],
        'shots_on_target' => ['home' => null, 'away' => null],
        'shots_on_target_1h' => ['home' => null, 'away' => null],
        'offsides' => ['home' => null, 'away' => null],
        'offsides_1h' => ['home' => null, 'away' => null],
        'fouls' => ['home' => null, 'away' => null],
        'fouls_1h' => ['home' => null, 'away' => null],
        'throw_ins' => ['home' => null, 'away' => null],
        'throw_ins_1h' => ['home' => null, 'away' => null],
        'shots_towards_goal' => ['home' => null, 'away' => null],
        'posts_crossbars' => ['home' => null, 'away' => null],
        'posts_crossbars_1h' => ['home' => null, 'away' => null]
    ];
}


function generateMockStatistics($match) {
    $homeScore = $match['score']['fullTime']['home'] ?? 0;
    $awayScore = $match['score']['fullTime']['away'] ?? 0;
    $homeScore1h = $match['score']['halfTime']['home'] ?? 0;
    $awayScore1h = $match['score']['halfTime']['away'] ?? 0;
    
    return [
        'corners' => [
            'home' => rand(3, 8) + ($homeScore * 2),
            'away' => rand(3, 8) + ($awayScore * 2)
        ],
        'corners_1h' => [
            'home' => rand(1, 4) + ($homeScore1h * 1),
            'away' => rand(1, 4) + ($awayScore1h * 1)
        ],
        'yellow_cards' => [
            'home' => rand(1, 4),
            'away' => rand(1, 4)
        ],
        'yellow_cards_1h' => [
            'home' => rand(0, 2),
            'away' => rand(0, 2)
        ],
        'red_cards' => [
            'home' => rand(0, 1) > 0.8 ? 1 : 0,
            'away' => rand(0, 1) > 0.8 ? 1 : 0
        ],
        'shots_on_target' => [
            'home' => rand(4, 10) + ($homeScore * 2),
            'away' => rand(4, 10) + ($awayScore * 2)
        ],
        'shots_on_target_1h' => [
            'home' => rand(2, 5) + ($homeScore1h * 1),
            'away' => rand(2, 5) + ($awayScore1h * 1)
        ],
        'offsides' => [
            'home' => rand(1, 5),
            'away' => rand(1, 5)
        ],
        'offsides_1h' => [
            'home' => rand(0, 3),
            'away' => rand(0, 3)
        ],
        'fouls' => [
            'home' => rand(8, 15),
            'away' => rand(8, 15)
        ],
        'fouls_1h' => [
            'home' => rand(4, 8),
            'away' => rand(4, 8)
        ],
        'throw_ins' => [
            'home' => rand(15, 25),
            'away' => rand(15, 25)
        ],
        'throw_ins_1h' => [
            'home' => rand(7, 12),
            'away' => rand(7, 12)
        ],
        'shots_towards_goal' => [
            'home' => rand(8, 18) + ($homeScore * 3),
            'away' => rand(8, 18) + ($awayScore * 3)
        ],
        'posts_crossbars' => [
            'home' => rand(0, 2),
            'away' => rand(0, 2)
        ],
        'posts_crossbars_1h' => [
            'home' => rand(0, 1),
            'away' => rand(0, 1)
        ]
    ];
}


function searchLeagues($search = '') {
    $params = [];
    if ($search) {
        $params['search'] = $search;
    }
    
    $result = makeFootballAPIRequest('/leagues', $params);
    
    if (!$result['success']) {
        return $result;
    }
    
    return [
        'success' => true,
        'leagues' => $result['data']['response'] ?? []
    ];
}


function getLeagueStandings($leagueCode = 'PL', $season = null) {
    if ($season === null) {
        
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

        if ($currentMonth >= 8) {
            $season = $currentYear;
        } else {
            $season = $currentYear - 1;
        }
    }
    
    if (strpos($season, '-') !== false) {
        $seasonParts = explode('-', $season);
        $season = $seasonParts[0];
    }
    
    $result = makeFootballAPIRequest("/competitions/{$leagueCode}/standings", [
        'season' => $season
    ], true, 300);
    
    if (!$result['success']) {
        return $result;
    }
    
    $data = $result['data'];
    $standings = [];
    
    if (isset($data['standings']) && is_array($data['standings'])) {
        foreach ($data['standings'] as $standingGroup) {
            if (isset($standingGroup['type']) && $standingGroup['type'] === 'TOTAL') {
                $table = $standingGroup['table'] ?? [];
                foreach ($table as $entry) {
                    $team = $entry['team'] ?? [];
                    $standings[] = [
                        'position' => $entry['position'] ?? 0,
                        'team' => [
                            'id' => $team['id'] ?? null,
                            'name' => $team['name'] ?? '',
                            'crest' => $team['crest'] ?? null
                        ],
                        'played' => $entry['playedGames'] ?? 0,
                        'won' => $entry['won'] ?? 0,
                        'drawn' => $entry['draw'] ?? 0,
                        'lost' => $entry['lost'] ?? 0,
                        'goals_for' => $entry['goalsFor'] ?? 0,
                        'goals_against' => $entry['goalsAgainst'] ?? 0,
                        'goal_difference' => $entry['goalDifference'] ?? 0,
                        'points' => $entry['points'] ?? 0,
                        'form' => $entry['form'] ?? null
                    ];
                }
            }
        }
    }
    
    return [
        'success' => true,
        'standings' => $standings,
        'league' => $data['competition']['name'] ?? '',
        'season' => $season
    ];
}


function getMatchLineups($fixtureId) {
    $result = makeFootballAPIRequest("/matches/{$fixtureId}", [], true, 300);
    
    if (!$result['success']) {
        return $result;
    }
    
    $match = $result['data'] ?? null;
    
    if (!$match) {
        return [
            'success' => false,
            'error' => 'Match not found'
        ];
    }
    
    $lineups = [
        'home' => null,
        'away' => null
    ];
    
    if (isset($match['homeTeam']['id'])) {
        $homeLineup = [
            'team' => [
                'id' => $match['homeTeam']['id'],
                'name' => $match['homeTeam']['name'],
                'crest' => $match['homeTeam']['crest'] ?? null
            ],
            'formation' => null,
            'coach' => null,
            'players' => []
        ];
        
        if (isset($match['lineups']) && is_array($match['lineups'])) {
            foreach ($match['lineups'] as $lineup) {
                if (isset($lineup['team']['id']) && $lineup['team']['id'] == $match['homeTeam']['id']) {
                    $homeLineup['formation'] = $lineup['formation'] ?? null;
                    $homeLineup['coach'] = $lineup['coach']['name'] ?? null;
                    
                    if (isset($lineup['startXI']) && is_array($lineup['startXI'])) {
                        foreach ($lineup['startXI'] as $player) {
                            $homeLineup['players'][] = [
                                'id' => $player['player']['id'] ?? null,
                                'name' => $player['player']['name'] ?? '',
                                'position' => $player['player']['position'] ?? null,
                                'shirt_number' => $player['player']['shirtNumber'] ?? null,
                                'grid_position' => $player['player']['grid'] ?? null,
                                'is_starting' => true
                            ];
                        }
                    }
                    
                    if (isset($lineup['substitutes']) && is_array($lineup['substitutes'])) {
                        foreach ($lineup['substitutes'] as $sub) {
                            $homeLineup['players'][] = [
                                'id' => $sub['player']['id'] ?? null,
                                'name' => $sub['player']['name'] ?? '',
                                'position' => $sub['player']['position'] ?? null,
                                'shirt_number' => $sub['player']['shirtNumber'] ?? null,
                                'grid_position' => null,
                                'is_starting' => false,
                                'is_substitute' => true
                            ];
                        }
                    }
                }
            }
        }
        
        $lineups['home'] = $homeLineup;
    }
    
    if (isset($match['awayTeam']['id'])) {
        $awayLineup = [
            'team' => [
                'id' => $match['awayTeam']['id'],
                'name' => $match['awayTeam']['name'],
                'crest' => $match['awayTeam']['crest'] ?? null
            ],
            'formation' => null,
            'coach' => null,
            'players' => []
        ];
        
        if (isset($match['lineups']) && is_array($match['lineups'])) {
            foreach ($match['lineups'] as $lineup) {
                if (isset($lineup['team']['id']) && $lineup['team']['id'] == $match['awayTeam']['id']) {
                    $awayLineup['formation'] = $lineup['formation'] ?? null;
                    $awayLineup['coach'] = $lineup['coach']['name'] ?? null;
                    
                    if (isset($lineup['startXI']) && is_array($lineup['startXI'])) {
                        foreach ($lineup['startXI'] as $player) {
                            $awayLineup['players'][] = [
                                'id' => $player['player']['id'] ?? null,
                                'name' => $player['player']['name'] ?? '',
                                'position' => $player['player']['position'] ?? null,
                                'shirt_number' => $player['player']['shirtNumber'] ?? null,
                                'grid_position' => $player['player']['grid'] ?? null,
                                'is_starting' => true
                            ];
                        }
                    }
                    
                    if (isset($lineup['substitutes']) && is_array($lineup['substitutes'])) {
                        foreach ($lineup['substitutes'] as $sub) {
                            $awayLineup['players'][] = [
                                'id' => $sub['player']['id'] ?? null,
                                'name' => $sub['player']['name'] ?? '',
                                'position' => $sub['player']['position'] ?? null,
                                'shirt_number' => $sub['player']['shirtNumber'] ?? null,
                                'grid_position' => null,
                                'is_starting' => false,
                                'is_substitute' => true
                            ];
                        }
                    }
                }
            }
        }
        
        $lineups['away'] = $awayLineup;
    }
    
    return [
        'success' => true,
        'lineups' => $lineups
    ];
}
?>
