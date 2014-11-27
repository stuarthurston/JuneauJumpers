DELETE FROM tblAttends
WHERE fnkMemberId IN (
    SELECT a.fnkMemberId
    FROM (SELECT * FROM tblAttends) a 
    WHERE a.fnkMemberId='0'
    AND a.fnkMemberId='0'
) AND fnkEventId IN (
    SELECT b.fnkEventId
    FROM (SELECT * FROM tblAttends) b 
    WHERE b.fnkEventId='12' 
    OR b.fnkEventId='37'
    OR b.fnkEventId='36'
);