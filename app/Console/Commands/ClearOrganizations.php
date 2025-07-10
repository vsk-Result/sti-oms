<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;

class ClearOrganizations extends HandledCommand
{
    protected $signature = 'oms:clear-organizations';

    protected $description = 'Находит и удаляет дубли организации';

    protected string $period = 'Вручную';

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

//        $array = [
//            'ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТ ЬЮ "ФИТАУТ ПРО"',
//            'ООО "Фитаут Солюшенс"',
//            'ООО ФИТАУТ',
//            'Общество с ограниченной ответственностью "ФИТАУТ ПРО"',
//            'ФИТАУТ ПРО ООО',
//            'Фитаут',
//            'Фитаут про',
//            'Супер Фит',
//            'Малафикт',
//            'Что-то еще'
//        ];


//        $array = [
//            'ООО "ПРОГРЕСС ЭВЕРЕСТ"',
//            'Общество с ограниченной ответственностью "ЭВЕРЕСТ КРЕПЕЖ"',
//            'ООО "ПРОГРЕСС ЭВЕРЕСТ"',
//            'ООО "ПРОГРЕСС ЭВЕРЕСТ"',
//            'ООО "ЭВЕРЕСТ КОНСАЛТИНГ"',
//            'Общество с ограниченной ответственностью "ЭВЕРЕСТ КРЕПЕЖ"',
//            'Общество с ограниченной ответственностью "ЭВЕРЕСТ КРЕПЕЖ"',
//            'Общество с ограниченной ответственностью "ЭВЕРЕСТ КРЕПЕЖ"',
//            'Общество с ограниченной ответственностью "ЭВЕРЕСТ КРЕПЕЖ"',
//            'ООО "ЭВЕРЕСТ КОНСАЛТИНГ"',
//            'ООО "ЭВЕРЕСТ КОНСАЛТИНГ"',
//            'ООО "ПРОГРЕСС ЭВЕРЕСТ"',
//            'ООО "ПРОГРЕСС ЭВЕРЕСТ"',
//            'ООО "ЭВЕРЕСТ КОНСАЛТИНГ"',
//        ];

//        function areStringsSimilar($str1, $str2, $threshold = 2) {
//            $normalizedStr1 = mb_strtolower(preg_replace('/[^a-zA-Zа-яА-ЯёЁ]/u', '', $str1));
//            $normalizedStr2 = mb_strtolower(preg_replace('/[^a-zA-Zа-яА-ЯёЁ]/u', '', $str2));
//
//            dd($str1, $normalizedStr1, $str2, $normalizedStr2);
//
//            $distance = levenshtein($normalizedStr1, $normalizedStr2);
//
//            return $distance <= $threshold;
//        }
//
//
//        $info = [];
//
//        foreach ($array as $key => $currentString) {
//            $c1 = str_replace('ООО ', '', $currentString);
//            $c1 = str_replace('Общество с ограниченной ответственностью ', '', $c1);
//            foreach ($array as $innerKey => $compareString) {
//                $c2 = str_replace('ООО ', '', $compareString);
//                $c2 = str_replace('Общество с ограниченной ответственностью ', '', $c2);
//
//                if ($key !== $innerKey && areStringsSimilar($c1, $c2)) {
//                    $info[] = "$c1 похожа на $c2\n";
//                }
//            }
//        }
//
//        dd($info);

        $deleted = [];
        $logs[] = '[' . now()->format('d.m.Y H:i:s') . '] Анализ дублирующихся контрагентов';
        $organizations = Organization::orderBy('name')->get();

        foreach ($organizations as $organization1) {

            if (in_array($organization1->id, $deleted)) {
                continue;
            }

            foreach ($organizations as $organization2) {
                if ($organization1->id === $organization2->id) {
                    continue;
                }

                if (in_array($organization2->id, $deleted)) {
                    continue;
                }

                if ($organization1->name === $organization2->name) {
                    if (!empty($organization1->inn) && empty($organization2->inn)) {
                        $logs[] = implode('**', [$organization2->id, $organization2->name, $organization2->inn, 'Дубликат по имени, без ИНН']);
                        $deleted[] = $organization2->id;
                        continue;
                    }

                    if (empty($organization1->inn) && !empty($organization2->inn)) {
                        $logs[] = implode('**', [$organization1->id, $organization1->name, $organization1->inn, 'Дубликат по имени, без ИНН']);
                        $deleted[] = $organization1->id;
                        continue;
                    }

                    if (!empty($organization1->inn) && !empty($organization2->inn)) {
                        if (!empty($organization1->kpp) && !empty($organization2->kpp)) {
                            $logs[] = implode('**', [$organization2->id, $organization2->name, $organization2->inn, 'Дубликат по имени, с ИНН и КПП']);
                            $deleted[] = $organization2->id;
                            continue;
                        }

                        if (!empty($organization1->kpp)) {
                            $logs[] = implode('**', [$organization2->id, $organization2->name, $organization2->inn, 'Дубликат по имени, с ИНН без КПП']);
                            $deleted[] = $organization2->id;
                            continue;
                        } else {
                            $logs[] = implode('**', [$organization1->id, $organization1->name, $organization1->inn, 'Дубликат по имени, с ИНН без КПП']);
                            $deleted[] = $organization1->id;
                            continue;
                        }
                    }

                    if (empty($organization1->inn) && empty($organization2->inn)) {
                        $logs[] = implode('**', [$organization2->id, $organization2->name, $organization2->inn, 'Дубликат по имени, без ИНН']);
                        $deleted[] = $organization2->id;
                        continue;
                    }
                }

                if ($organization1->inn == $organization2->inn && ! empty($organization1->inn) && ! empty($organization2->inn)) {
                    if (!empty($organization1->kpp) && !empty($organization2->kpp)) {
                        $logs[] = implode('**', [$organization2->id, $organization2->name, $organization2->inn, 'Дубликат по ИНН, с КПП']);
                        $deleted[] = $organization2->id;
                        continue;
                    }

                    if (!empty($organization1->kpp)) {
                        $logs[] = implode('**', [$organization2->id, $organization2->name, $organization2->inn, 'Дубликат по ИНН, без КПП']);
                        $deleted[] = $organization2->id;
                    } else {
                        $logs[] = implode('**', [$organization1->id, $organization1->name, $organization1->inn, 'Дубликат по ИНН, без КПП']);
                        $deleted[] = $organization1->id;
                    }
                }
            }
        }

        $logs[] = '[' . now()->format('d.m.Y H:i:s') . '] Анализ завершен';

        foreach ($logs as $log) {
            Log::channel('organizations_delete_duplication')->info($log);
        }

        $this->endProcess();

        return 0;
    }
}
