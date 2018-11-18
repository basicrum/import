<?php

declare(strict_types=1);

class BasicRum_Import_Csv_Query_NavigationTimings
{

    public function navigationUrlInsert($url, $time)
    {
        $table = 'navigation_timings_urls';
    }

    public function navigationTimingInsert(array $navigationTimings)
    {
        $t = 'navigation_timings';

        $f = implode(',', array_keys($navigationTimings));
        $v = implode(',', array_values($navigationTimings));

        $q = "INSERT INTO %s (%s) VALUES (%s)";

        return sprintf($q, $t, $f, $v);

        return "INSERT INTO navigation_timings
            (url_id,
            boomerang_version,
            vis_st,
            ua_plt,
            ua_vnd,
            pid,
            nt_red_cnt,
            nt_nav_type,
            nt_nav_st,
            nt_red_st,
            nt_red_end,
            nt_fet_st,
            nt_dns_st,
            nt_dns_end,
            nt_con_st,
            nt_con_end,
            nt_req_st,
            nt_res_st,
            nt_res_end,
            nt_domloading,
            nt_domint,
            nt_domcontloaded_st,
            nt_domcontloaded_end,
            nt_domcomp,
            nt_load_st,
            nt_load_end,
            nt_unload_st,
            nt_unload_end,
            nt_spdy,
            nt_cinf,
            nt_first_paint,
            guid,
            created_at,
            user_agent,
            pt_fp,
            pt_fcp)

            VALUES ('{$navigationTimings['url_id']}',
            '{$navigationTimings['v']}',
            '{$navigationTimings['vis_st']}',
            '{$navigationTimings['ua_plt']}',
            '{$navigationTimings['ua_vnd']}',
            '{$navigationTimings['pid']}',
            '{$navigationTimings['nt_red_cnt']}',
            '{$navigationTimings['nt_nav_type']}',
            '{$navigationTimings['nt_nav_st']}',
            '{$navigationTimings['nt_red_st']}',
            '{$navigationTimings['nt_red_end']}',
            '{$navigationTimings['nt_fet_st']}',
            '{$navigationTimings['nt_dns_st']}',
            '{$navigationTimings['nt_dns_end']}',
            '{$navigationTimings['nt_con_st']}',
            '{$navigationTimings['nt_con_end']}',
            '{$navigationTimings['nt_req_st']}',
            '{$navigationTimings['nt_res_st']}',
            '{$navigationTimings['nt_res_end']}',
            '{$navigationTimings['nt_domloading']}',
            '{$navigationTimings['nt_domint']}',
            '{$navigationTimings['nt_domcontloaded_st']}',
            '{$navigationTimings['nt_domcontloaded_end']}',
            '{$navigationTimings['nt_domcomp']}',
            '{$navigationTimings['nt_load_st']}',
            '{$navigationTimings['nt_load_end']}',
            '{$navigationTimings['nt_unload_st']}',
            '{$navigationTimings['nt_unload_end']}',
            '{$navigationTimings['nt_spdy']}',
            '{$navigationTimings['nt_cinf']}',
            '{$navigationTimings['nt_first_paint']}',
            '{$navigationTimings['guid']}',
            '{$navigationTimings['created_at']}',
            '{$navigationTimings['user_agent']}',
            '{$navigationTimings['pt_fp']}',
            '{$navigationTimings['pt_fcp']}');";
    }

}