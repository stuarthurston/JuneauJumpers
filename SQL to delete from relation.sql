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



DELETE FROM tblAttends
WHERE fnkMemberId IN (
    SELECT a.fnkMemberId
    FROM (SELECT * FROM tblAttends) a 
    where (fnkMemberId = '23379' and fnkEventId = '36') 
    OR (fnkMemberId = '23389' and fnkEventId = '37')
);  



Select * from tblAttends where (fnkMemberId = '0' and fnkEventId = '36') or (fnkMemberId = '0' and fnkEventId = '37');




DELETE 
  entry
  FROM tblAttends AS entry 
CROSS JOIN (
  SELECT fnkMemberId, fnkEventId FROM tblAttends 
where (fnkMemberId = '0' and fnkEventId = '12')
) AS x
USING (fnkMemberId, fnkEventId);

