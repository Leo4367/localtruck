<?php

namespace App\Console\Commands;

use App\Mail\BrokerInquiryMail;
use App\Mail\TestEmail;
use App\Models\Appointment;
use Carbon\Traits\Date;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->{$this->argument('name')}();
    }

    protected function time_zone()
    {
        $appointment = Appointment::first();
        // 在控制器或模型中，格式化时间为应用的时区
        return $appointment->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s');
    }

    protected function aaa($words, $maxWidth)
    {
        $result = [];


        $wordsTwo = [];  // words 分割成二维数组
        $tmpLen = 0;   // 记录处理行的长度变量，如果 > $maxWidth 就 = 0
        $tmpArr = [];  // 记录处理行的内容变量，如果 $tmpLen > $maxWidth 就 = []
        for ($i = 0; $i < count($words); $i++) {
            $tmpArr[] = $words[$i];  // 先将 $i 放入 $tmpArr

            // 临时记录长度 + 当前字符长度 + 单词间的最少一个空格(第一个单词不加空格)
            $tmpLen += strlen($words[$i]) + (count($tmpArr) > 1 ? 1 : 0);

            // 大于$maxWidth。 踢出当前最后一个单词，$i回退一格，保存当前行，清空当前行
            if ($tmpLen > $maxWidth) {
                array_pop($tmpArr);
                $wordsTwo[] = $tmpArr;
                $tmpLen = 0;
                $tmpArr = [];
                $i--;
            }
        }
        // 最后次处理是否存在内容
        if (count($tmpArr)) $wordsTwo[] = $tmpArr;

        // 补充空格
        $wordsTwoLen = count($wordsTwo);
        for ($i = 0; $i < $wordsTwoLen; $i++) {

            // 要补的全部空格长度
            $spaceLen = $maxWidth - strlen(implode('', $wordsTwo[$i]));

            // 当前行长度
            $iArrLen = count($wordsTwo[$i]);

            // 当行只包含一个单词为左对齐
            if ($iArrLen == 1) {
                $result[] = $wordsTwo[$i][0] . $this->makeSpace($spaceLen);
                continue;
            }
            // 最后一行应为左对齐，且单词之间不插入额外的空格
            if (($wordsTwoLen - 1) == $i) {
                $lastStr = implode(' ', $wordsTwo[$i]);
                $result[] = $lastStr . $this->makeSpace($maxWidth - strlen($lastStr));
                continue;
            }

            $makeSpaceLen = $spaceLen / ($iArrLen - 1);
            // 如果可以整除，均铺
            if ($makeSpaceLen == intval($makeSpaceLen)) {
                $spaceStr = $this->makeSpace($makeSpaceLen);
                $result[] = implode($spaceStr, $wordsTwo[$i]);
                continue;
            }

            $rowStr = '';
            // 未整除  则左侧放置的空格数要多于右侧的空格数
            $iArrLenTmp = $iArrLen;
            $makeSpaceLenCeil = ceil($makeSpaceLen);  //先得到多的空格
            for ($j = 0; $j < $iArrLen; $j++) {

                $rowStr .= $wordsTwo[$i][$j];

                // 剩余拼接的长度小于
                if ($spaceLen < $makeSpaceLenCeil) continue;

                // 先拼上最大的 ，剩下的再去分
                $rowStr .= $this->makeSpace($makeSpaceLenCeil);
                // 拼接空格数量更新 总长缩减
                $spaceLen = $spaceLen - $makeSpaceLenCeil;
                // 去掉当前单词，剩下的单词再去分
                $iArrLenTmp--;
                if ($iArrLenTmp == 1) {
                    $makeSpaceLenCeil = 1;
                } else {
                    $makeSpaceLen = $spaceLen / ($iArrLenTmp - 1);
                    $makeSpaceLenCeil = ceil($makeSpaceLen);
                }
            }
            $result[] = $rowStr;
        }

        return $result;
    }


    protected function fullJustify()
    {
        $words = ["This", "is", "an", "example", "of", "text", "justification."];
        $maxWidth = 16;

        $wordsTwo = [];
        $tempLen = 0;
        $tempArr = [];
        for ($i = 0; $i < count($words); $i++) {
            $tempArr[] = $words[$i];
            $tempLen += strlen($words[$i]) + (count($tempArr) > 1 ? 1 : 0);

            if ($tempLen > $maxWidth) {
                array_pop($tempArr);
                $wordsTwo[] = $tempArr;
                $tempLen = 0;
                $tempArr = [];
                $i--;
            }
        }
        dd($wordsTwo);
    }

    // 生成空格
    function makeSpace($len, $i = 0, $res = '')
    {
        while ($i++ < $len) {
            $res .= ' ';
        }
        return $res;
    }

    protected function testemail()
    {
        Mail::to('websupport@del-ev.com')->send(new TestEmail());
        dd('Test email sent!');
    }

}
