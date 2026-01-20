<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KnowledgeController extends Controller
{
    public function index(): View
    {
        $explanations = [
            [
                'name' => 'Сводная информация карточки объекта',
                'ext' => 'doc',
                'size' => '16 Кб',
                'file' => 'Сводная_информация_карточки_объекта.docx'
            ],
            [
                'name' => 'Отчет доходов и расходов объекта',
                'ext' => 'doc',
                'size' => '113 Кб',
                'file' => 'Отчет_доходов_и_расходов_объекта.docx'
            ],
            [
                'name' => 'Отчет стоимости рабочих объекта',
                'ext' => 'doc',
                'size' => '113 Кб',
                'file' => 'Отчет_стоимости_рабочих_объекта.docx'
            ]
        ];

        $instructions = [
            [
                'name' => 'Загрузка долгов объекта по подрядчикам',
                'ext' => 'pdf',
                'size' => '113 Кб',
                'file' => 'Загрузка_долгов_объекта_по_подрядчика.pdf'
            ],
            [
                'name' => 'Использование Helpdesk',
                'ext' => 'pdf',
                'size' => '544 Кб',
                'file' => 'Использование_Helpdesk.pdf'
            ],
            [
                'name' => 'План налогов к оплате',
                'ext' => 'pdf',
                'size' => '835 Кб',
                'file' => 'План_налогов_к_оплате.pdf',
            ],
            [
                'name' => 'Комментарии в Cash Flow',
                'ext' => 'doc',
                'size' => '127 Кб',
                'file' => 'Комментарии_в_Cash_Flow.docx'
            ],
            [
                'name' => 'Проверка не совпадения приходов от заказчиков и разбивки приходов',
                'ext' => 'doc',
                'size' => '371 Кб',
                'file' => 'Проверка_не_совпадения_приходов_от_заказчиков_и_разбив_и_приходов.docx'
            ],
            [
                'name' => 'Проверка оплат в ОМС закрытых периодах касс CRM перед их закрытием',
                'ext' => 'doc',
                'size' => '814 Кб',
                'file' => 'Проверка_оплат_в_ОМС_закрытых_периодах_касс_CRM_перед_их_закрытием.docx',
            ],
            [
                'name' => 'Разбивка налогов из файла 1С',
                'ext' => 'doc',
                'size' => '236 Кб',
                'file' => 'Разбивка_налогов_из_файла_1С.docx'
            ],
            [
                'name' => 'Ручное обновление долгов объекта в ОМС',
                'ext' => 'doc',
                'size' => '535 Кб',
                'file' => 'Ручное_обновление_долгов_объекта_в_ОМС.docx',
            ],
        ];

        return view('knowledge.index', compact('instructions', 'explanations'));
    }
}
