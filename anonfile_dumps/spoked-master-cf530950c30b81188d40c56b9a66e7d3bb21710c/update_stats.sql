DELIMITER $$

DROP PROCEDURE IF EXISTS update_stats$$
CREATE PROCEDURE update_stats(sent_limit INT)
BEGIN
    DECLARE active_date DATETIME;
    DECLARE sent_results BIGINT DEFAULT 0;

    SELECT @active_date:=updated_at FROM emails WHERE status = 1;

    SELECT @sent_results:=count(*) FROM connection_results
    WHERE add_date >= @active_date;

    SELECT @still_active:=count(*) FROM connection_results
    WHERE add_date >= @active_date AND add_date >= CURRENT_TIMESTAMP() - INTERVAL 2 hour;

    IF @sent_results > sent_limit AND @still_active = 0 THEN
        UPDATE emails SET status = 2 WHERE status = 1;
    END IF;
END;
