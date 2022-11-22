DELIMITER $$
DROP PROCEDURE IF EXISTS FIX_EXPNS_TRNSCT;
CREATE PROCEDURE FIX_EXPNS_TRNSCT
(
    IN _stnd_dt  VARCHAR(8)
)
/*
@DESCRIPTION
    고정지출 건별로 현금은 지출 처리, 카드의 경우 입출금 처리한다
@PARAM

@RETURN
    RESULT : 실패(-1), 성공 (0)
*/

BEGIN
    /* 종료 구분 변수 */
    DECLARE _done INT DEFAULT FALSE;
    DECLARE dbErr int default 0;

    DECLARE _stnd_dt_cls    VARCHAR(1);
    DECLARE _bef_bsns_dt    VARCHAR(8);

    /* 테이블의  컬럼값을 담을 변수 */
    DECLARE _db_no          VARCHAR(10);
    DECLARE _fix_expns_srno INT        ;
    DECLARE _expns_nm       VARCHAR(50);
    DECLARE _sttlmt_yn      VARCHAR(1);
    DECLARE _io_tr_cls      VARCHAR(3);
    DECLARE _expns_chnl_cls VARCHAR(2);
    DECLARE _expns_cls      VARCHAR(5);
    DECLARE _whr_to_buy     VARCHAR(50);
    DECLARE _memo           VARCHAR(100);
    DECLARE _bank           VARCHAR(2);
    DECLARE _ac_no          VARCHAR(20);
    DECLARE _rel_ac_no      VARCHAR(20);

    DECLARE _amt            DOUBLE;
    /* 테이블의  컬럼값을 담을 변수 */

    DECLARE _io_tr_srno         DOUBLE;
    DECLARE _expns_srno         DOUBLE;
    DECLARE _prcs_cnt           DOUBLE DEFAULT 0;
    DECLARE _sttlmt_prcs_cnt    DOUBLE DEFAULT 0;
    DECLARE _expns_prcs_cnt     DOUBLE DEFAULT 0;


    /* 고정지출 리스트를 읽어오는 커서를 만든다. */
    DECLARE cur_fix_expns_list CURSOR FOR
        SELECT  a.db_no
               ,a.fix_expns_srno
               ,a.expns_nm
               ,a.sttlmt_yn
               ,a.io_tr_cls
               ,a.expns_chnl_cls
               ,a.expns_cls
               ,a.whr_to_buy
               ,a.memo
               ,a.bank
               ,a.ac_no
               ,a.rel_ac_no
               ,a.amt
          FROM  tbb005l00  a
         WHERE  ifnull(a.trnsfr_day, a.expns_day) >= (
                                                     SELECT  case when a.dt_cls = '1' AND b.DT_CLS <> '1' then SUBSTR(c.bf_dt, 7, 2)
                                                                  when a.dt_cls = '1' and b.dt_cls = '1' then SUBSTR(a.dt, 7, 2)
				                                                  ELSE '99' END  bf_dt
                                                       FROM  tba004l00  a
                                                            ,tba004l00  b
                                                            ,(
                                                              SELECT  date_format(adddate(STR_TO_DATE(max(a.dt), '%Y%m%d'), 1), '%Y%m%d')  bf_dt
                                                                FROM  tba004l00  a
                                                               WHERE  a.dt >= date_format(adddate(STR_TO_DATE(_stnd_dt, '%Y%m%d'), -10), '%Y%m%d')
                                                                 AND  a.dt < _stnd_dt
                                                                 AND  a.dt_cls = '1'
                                                             )  c
                                                      WHERE  a.dt = _stnd_dt
                                                        AND  b.DT = date_format(adddate(STR_TO_DATE(_stnd_dt, '%Y%m%d'), -1), '%Y%m%d')
                                                    )
           AND  ifnull(a.trnsfr_day, a.expns_day) <= (
                                                      SELECT  case when a.DT_CLS = '1' then SUBSTR(a.dt, 7, 2) ELSE '99' end
                                                        FROM  tba004l00  a
                                                       WHERE  a.dt = _stnd_dt
                                                     )
           AND  a.del_yn = 'N'
           AND  NOT EXISTS (
                            SELECT  1
                              FROM  tbb001l00  z
                             WHERE  z.DB_NO = a.DB_NO
                               AND  z.expns_dt = _stnd_dt
                               AND  z.mnpl_ip  = 'FIX_EXPNS_TRNSCT'
                               AND  z.FIX_EXPNS_SRNO = a.FIX_EXPNS_SRNO
                           )
           ;

    /* 커서 종료조건 : 더이상 없다면 종료 */
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION  SET dbErr = -1;

    INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', 'BEGIN', NOW());
    INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_stnd_dt = ', _stnd_dt), NOW());

    /* 커서를 열어준다. */
    OPEN cur_fix_expns_list;

    read_loop: LOOP
        FETCH cur_fix_expns_list INTO _db_no
                                     ,_fix_expns_srno
                                     ,_expns_nm
                                     ,_sttlmt_yn
                                     ,_io_tr_cls
                                     ,_expns_chnl_cls
                                     ,_expns_cls
                                     ,_whr_to_buy
                                     ,_memo
                                     ,_bank
                                     ,_ac_no
                                     ,_rel_ac_no
                                     ,_amt ;

        IF _done THEN
            INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('Done _prcs_cnt = ', IFNULL(_prcs_cnt, 'NULL')), NOW());
            LEAVE read_loop;
        END IF;

        SET _prcs_cnt = _prcs_cnt + 1;

        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_prcs_cnt = ', IFNULL(_prcs_cnt, 'NULL')), NOW());
        
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_fix_expns_srno = ', IFNULL(_fix_expns_srno, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_expns_nm = ', IFNULL(_expns_nm, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_sttlmt_yn = ', IFNULL(_sttlmt_yn, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_io_tr_cls = ', IFNULL(_io_tr_cls, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_expns_chnl_cls = ', IFNULL(_expns_chnl_cls, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_expns_cls = ', IFNULL(_expns_cls, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_whr_to_buy = ', IFNULL(_whr_to_buy, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_memo = ', IFNULL(_memo, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_bank = ', IFNULL(_bank, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_ac_no = ', IFNULL(_ac_no, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_rel_ac_no = ', IFNULL(_rel_ac_no, 'NULL')), NOW());
        INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_amt = ', IFNULL(_amt, 'NULL')), NOW());
        

        /* Loop 안에서 매번 Commit or Rollback 처리 */
        START TRANSACTION;

        /* 결제의 경우 출금 처리*/
        IF _sttlmt_yn = 'Y' THEN
            SELECT  (CLM_VAL + 1) INTO _io_tr_srno FROM tba002i00 WHERE CLM_NM = 'IO_TR_SRNO';

            INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_io_tr_srno = ', IFNULL(_io_tr_srno, 'NULL')), NOW());

            UPDATE  tba002i00
               SET  CLM_VAL = _io_tr_srno
                   ,MNPL_USR_NO = 'BATCH'
                   ,MNPL_IP     = 'FIX_EXPNS_TRNSCT'
                   ,MNPL_YMDH   = NOW()
             WHERE  DB_NO = _db_no
               AND  CLM_NM = 'IO_TR_SRNO'
            ;

            INSERT INTO tbb003l00
                (DB_NO
                ,IO_TR_SRNO
                ,DT
                ,IO_TR_CLS
                ,MEMO
                ,AMT
                ,DEL_YN
                ,MNPL_USR_NO
                ,MNPL_IP
                ,MNPL_YMDH
                )
            VALUES
                (_db_no
                ,_io_tr_srno
                ,_stnd_dt
                ,_io_tr_cls
                ,CASE WHEN _memo IS NOT NULL THEN _memo
                      ELSE NULL
                 END
                ,_amt
                ,'N'
                ,'BATCH'
                ,'FIX_EXPNS_TRNSCT'
                ,NOW()
                );

            IF dbErr < 0 THEN
                ROLLBACK;
            ELSE
                COMMIT;
            END IF;

            INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('sttlmt dbErr = ', IFNULL(dbErr, 'NULL')), NOW());

            SET _sttlmt_prcs_cnt = _sttlmt_prcs_cnt + 1;

        /* 결제가 아닌 경우 지출 처리*/
        ELSE
            SELECT  (CLM_VAL + 1) INTO _expns_srno FROM tba002i00 WHERE CLM_NM = 'EXPNS_SRNO';

            INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_expns_srno = ', IFNULL(_expns_srno, 'NULL')), NOW());

            UPDATE  tba002i00
               SET  CLM_VAL = _expns_srno
                   ,MNPL_USR_NO = 'BATCH'
                   ,MNPL_IP     = 'FIX_EXPNS_TRNSCT'
                   ,MNPL_YMDH   = NOW()
             WHERE  DB_NO = _db_no
               AND  CLM_NM = 'EXPNS_SRNO'
            ;

            INSERT INTO tbb001l00
                (DB_NO
                ,EXPNS_SRNO
                ,EXPNS_DT
                ,EXPNS_CHNL_CLS
                ,EXPNS_CLS
                ,MEMO
                ,WHR_TO_BUY
                ,AMT
                ,SSAMZI_YN
                ,COST_CLS
                ,FIX_EXPNS_SRNO
                ,AC_NO
                ,REL_AC_NO
                ,DEL_YN
                ,MNPL_USR_NO
                ,MNPL_IP
                ,MNPL_YMDH
                )
            VALUES (_db_no
                   ,_expns_srno
                   ,_stnd_dt
                   ,_expns_chnl_cls
                   ,_expns_cls
                   ,CASE WHEN _memo IS NOT NULL THEN CONCAT(_expns_nm, '(', _memo, ')')
                         ELSE _expns_nm
                    END
                   ,_whr_to_buy
                   ,_amt
                   ,'N'
                   ,'1'
                   ,_fix_expns_srno
                   ,_ac_no
                   ,_rel_ac_no
                   ,'N'
                   ,'BATCH'
                   ,'FIX_EXPNS_TRNSCT'
                   ,NOW()
                   );

            IF dbErr < 0 THEN
                ROLLBACK;
            ELSE
                COMMIT;
            END IF;

            INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('expns dbErr = ', IFNULL(dbErr, 'NULL')), NOW());

            SET _expns_prcs_cnt = _expns_prcs_cnt + 1;

        END IF;

    END LOOP;

    /* 커서를 닫아준다. */
    CLOSE cur_fix_expns_list;

    INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_sttlmt_prcs_cnt = ', IFNULL(_sttlmt_prcs_cnt, 0)), NOW());
    INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', concat('_expns_prcs_cnt = ', IFNULL(_expns_prcs_cnt, 0)), NOW());

    INSERT INTO tbz099l00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('FIX_EXPNS_TRNSCT', 'END', NOW());

END$$

DELIMITER ;
