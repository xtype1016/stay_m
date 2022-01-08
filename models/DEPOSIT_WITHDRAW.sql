DELIMITER $$
DROP PROCEDURE IF EXISTS DEPOSIT_WITHDRAW;
CREATE PROCEDURE DEPOSIT_WITHDRAW
(
    IN _stnd_dt VARCHAR(8)
)
/*
@DESCRIPTION
    체크아웃한 고객의 보증금 자동 환불 처리
@PARAM

@RETURN
    RESULT : 실패(-1), 성공 (0)
*/

BEGIN
    /* 종료 구분 변수 */
    DECLARE _done INT DEFAULT FALSE;

    /* 테이블 컬럼값을 담을 변수 */
    DECLARE _db_no          VARCHAR(10);
    DECLARE _rsv_srno       DOUBLE;
    DECLARE _deposit        DOUBLE;

    DECLARE _tr_srno        DOUBLE;
    
    DECLARE _prcs_cnt           DOUBLE DEFAULT 0;

    /* 고정지출 리스트를 읽어오는 커서를 만든다. */
    DECLARE CUR_DEPOSIT_LIST CURSOR FOR
        SELECT  db_no, rsv_srno, deposit
          FROM  TBA005L00
         WHERE  date_format(ADDDATE(str_to_date(END_DT, '%Y%m%d'), 1), '%Y%m%d') = _stnd_dt
           AND  DEPOSIT > 0
           AND  CNCL_YN = 'N';

    /* 커서 종료조건 : 더이상 없다면 종료 */
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET _done = TRUE;

    INSERT INTO TBZ099L00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('DEPOSIT_WITHDRAW', 'BEGIN', NOW());
    INSERT INTO TBZ099L00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('DEPOSIT_WITHDRAW', concat('_stnd_dt = ', _stnd_dt), NOW());

    /* 커서를 열어준다. */
    OPEN CUR_DEPOSIT_LIST;

    read_loop: LOOP
        FETCH CUR_DEPOSIT_LIST INTO _db_no, _rsv_srno, _deposit;

        IF _done THEN
            INSERT INTO TBZ099L00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('DEPOSIT_WITHDRAW', concat('_prcs_cnt = ', IFNULL(_prcs_cnt, 0)), NOW());
            LEAVE read_loop;
        END IF;

        SET _prcs_cnt = _prcs_cnt + 1;

        SELECT  (CLM_VAL + 1) INTO _tr_srno FROM TBA002I00 WHERE DB_NO = _db_no AND CLM_NM = 'TR_SRNO';

            UPDATE  TBA002I00
               SET  CLM_VAL     = _tr_srno
                   ,MNPL_USR_NO = 'BATCH'
                   ,MNPL_IP     = 'DEPOSIT_WITHDRAW'
                   ,MNPL_YMDH   = NOW()
             WHERE  DB_NO  = _db_no
               AND  CLM_NM = 'TR_SRNO'
            ;


            INSERT INTO TBA006L00
                (DB_NO
                ,TR_SRNO
                ,RSV_SRNO
                ,TR_DT
                ,TR_CLS
                ,TR_CHNL_CLS
                ,AMT
                ,MEMO
                ,OTHR_WITHDRAW_YN
                ,DEL_YN
                ,MNPL_USR_NO
                ,MNPL_IP
                ,MNPL_YMDH
                )
            VALUES
                (_db_no
                ,_tr_srno
                ,_rsv_srno
                ,_stnd_dt
                ,'32'
                ,'2'
                ,_deposit
                ,'체크아웃 보증금 환불'
                ,'N'
                ,'N'
                ,'BATCH'
                ,'DEPOSIT_WITHDRAW'
                ,NOW()
                );

    END LOOP;

    /* 커서를 닫아준다. */
    CLOSE CUR_DEPOSIT_LIST;
    
    INSERT INTO TBZ099L00 (PGM_NM, MEMO, MNPL_YMDH) VALUES('DEPOSIT_WITHDRAW', 'END', NOW());
END$$

DELIMITER ;
