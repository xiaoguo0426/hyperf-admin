<?php

namespace App\Command\Schedule;

use App\Model\AmazonAppModel;
use App\Util\Amazon\ScheduleReportCreator;
use App\Util\AmazonApp;
use Carbon\Carbon;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class ScheduleCreateReport extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('schedule:report:create');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->setDescription('定时创建指定报告类型指定时间范围报告');
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $report_schedule_list = [
            (new ScheduleReportCreator(
                'GET_SALES_AND_TRAFFIC_REPORT',
                Carbon::now()->subDays(10),
                Carbon::now()->subDays(3),
                true,
                true
            ))
        ];

        AmazonApp::trigger(function (AmazonAppModel $amazonAppCollection) use ($report_schedule_list) {
            $merchant_id = $amazonAppCollection->merchant_id;
            $merchant_store_id = $amazonAppCollection->merchant_store_id;

            foreach ($report_schedule_list as $reportScheduleCreator) {
                /**
                 * @var ScheduleReportCreator $reportScheduleCreator
                 */
                $this->call('amazon:report:create', [
                    'merchant_id' => $merchant_id,
                    'merchant_store_id' => $merchant_store_id,
                    'report_type' => $reportScheduleCreator->getReportType(),
                    '--report_start_date' => $reportScheduleCreator->getStartDate(),
                    '--report_end_date' => $reportScheduleCreator->getEndDate(),
                    '--is_range_date' => $reportScheduleCreator->getIsRangeDate(),
                    '--is_force_create' => $reportScheduleCreator->getIsForceCreate(),
                ]);
            }
        });

    }
}