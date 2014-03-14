<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
?>

<header id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div class="to_content"><a href="#container">본문 바로가기</a></div>

    <?php if(defined('_INDEX_')) { // index에서만 실행 ?>
    <div id="hd_pop">
        <h2>팝업레이어 알림</h2>
        <?php include G5_MOBILE_PATH.'/newwin.inc.php'; // 팝업레이어 ?>
    </div>
    <?php } ?>
    <div class="to_content"><a href="#gnb">메인메뉴 바로가기</a></div>

    <div id="hd_wrapper">

        <div id="logo">
            <a href="<?php echo G5_URL ?>"><img src="<?php echo G5_IMG_URL ?>/logo.jpg" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>

        <button type="button" id="hd_menu_open">메뉴<span class="sound_only"> 열기</span></button>

        <div id="menu_wrap">
            <ul>
            <?php
            $sql = " select *
                        from {$g5['menu_table']}
                        where me_mobile_use = '1'
                          and length(me_code) = '2'
                        order by me_order, me_id ";
            $result = sql_query($sql);

            for($i=0; $row=sql_fetch_array($result); $i++) {
            ?>
                <li>
                    <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                    <?php
                    $sql2 = " select *
                                from {$g5['menu_table']}
                                where me_mobile_use = '1'
                                  and length(me_code) = '4'
                                  and substring(me_code, 1, 2) = '{$row['me_code']}'
                                order by me_order, me_id ";
                    $result2 = sql_query($sql2);

                    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
                        if($k == 0)
                            echo '<ul class="gnb_2dul">'.PHP_EOL;
                    ?>
                        <li class="gnb_2dli"><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><?php echo $row2['me_name'] ?></a></li>
                    <?php
                    }

                    if($k > 0)
                        echo '</ul>'.PHP_EOL;
                    ?>
                </li>
            <?php
            }

            if ($i == 0) {  ?><li class="gnb_empty">생성된 메뉴가 없습니다.</li><?php }
            ?>
            </ul>
            <button type="button" id="hd_menu_close">닫기</button>
        </div>
        <script>
        $(function() {
            $("#hd_menu_open").click(function() {
                var $menu = $("#menu_wrap");
                var w = $menu.width();
                var sw = $(window).width();

                $menu
                    .css({
                        display: "none",
                        left: sw+"px"
                    })
                    .css("display", "block")
                    .animate(
                        { left: "-="+w+"px" }, 1500
                    );
            });

            $("#hd_menu_close").click(function() {
                var $menu = $("#menu_wrap");
                var w = $menu.width();
                var sw = $(window).width();

                $menu.animate(
                        { left: "+="+w+"px" }, 1500,
                        function() {
                            $menu.css("display", "none");
                        }
                    );
            });
        });
        </script>

        <button type="button" id="hd_sch_open">검색<span class="sound_only"> 열기</span></button>

        <aside id="hd_sch">
            <div class="sch_inner">
                <h2>사이트 내 전체검색</h2>
                <form name="fsearchbox" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);" method="get">
                <input type="hidden" name="sfl" value="wr_subject||wr_content">
                <input type="hidden" name="sop" value="and">
                <input type="text" name="stx" id="sch_stx" placeholder="검색어(필수)" required class="frm_input required" maxlength="20">
                <input type="submit" value="검색" class="btn_submit">
                <button type="button" class="pop_close"><span class="sound_only">검색 </span>닫기</button>
                </form>

                <script>
                function fsearchbox_submit(f)
                {
                    if (f.stx.value.length < 2) {
                        alert("검색어는 두글자 이상 입력하십시오.");
                        f.stx.select();
                        f.stx.focus();
                        return false;
                    }

                    // 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
                    var cnt = 0;
                    for (var i=0; i<f.stx.value.length; i++) {
                        if (f.stx.value.charAt(i) == ' ')
                            cnt++;
                    }

                    if (cnt > 1) {
                        alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
                        f.stx.select();
                        f.stx.focus();
                        return false;
                    }

                    return true;
                }
                </script>
            </div>
        </aside>
        <script>
            $(function (){
                var $hd_sch = $("#hd_sch");
                $("#hd_sch_open").click(function(){
                    $hd_sch.css("display","block");
                });
                $("#hd_sch .pop_close").click(function(){
                    $hd_sch.css("display","none");
                });
            });
        </script>

        <ul id="hd_nb">
            <li><a href="<?php echo G5_BBS_URL ?>/qalist.php" id="snb_new">1:1문의</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/current_connect.php" id="snb_cnt">접속자 <?php echo connect(); // 현재 접속자수 ?></a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/new.php" id="snb_new">새글</a></li>
            <?php if ($is_member) { ?>
            <?php if ($is_admin) { ?>
            <li><a href="<?php echo G5_ADMIN_URL ?>" id="snb_adm"><b>관리자</b></a></li>
            <?php } ?>
            <li><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php" id="snb_modify">정보수정</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/logout.php" id="snb_logout">로그아웃</a></li>
            <?php } else { ?>
            <li><a href="<?php echo G5_BBS_URL ?>/register.php" id="snb_join">회원가입</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/login.php" id="snb_login">로그인</a></li>
            <?php } ?>
        </ul>

    </div>
</header>

<hr>

<div id="wrapper">
    <div id="aside">
        <?php echo outlogin('basic'); // 외부 로그인 ?>
    </div>
    <div id="container">
        <?php if ((!$bo_table || $w == 's' ) && !defined("_INDEX_")) { ?><div id="container_title"><?php echo $g5['title'] ?></div><?php } ?>
        <div id="text_size">
            <!-- font_resize('엘리먼트id', '제거할 class', '추가할 class'); -->
            <button id="size_down" onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="<?php echo G5_URL; ?>/img/ts01.gif" alt="기본"></button>
            <button id="size_def" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="<?php echo G5_URL; ?>/img/ts02.gif" alt="크게"></button>
            <button id="size_up" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="<?php echo G5_URL; ?>/img/ts03.gif" alt="더크게"></button>
        </div>