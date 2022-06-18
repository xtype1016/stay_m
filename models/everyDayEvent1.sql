DELIMITER $$
DROP EVENT `everyDayEvent1`;
CREATE EVENT `everyDayEvent1`
	ON SCHEDULE
		EVERY 1 DAY STARTS '2020-09-12 17:00:00'
	ON COMPLETION PRESERVE
	ENABLE
	COMMENT ''
	DO
	    BEGIN
        	SELECT  @_stnd_dt := date_format(CURDATE(), '%Y%m%d');
        	CALL FIX_EXPNS_TRNSCT(@_stnd_dt);
        	CALL DEPOSIT_WITHDRAW(@_stnd_dt);
        END $$
DELIMITER ;
