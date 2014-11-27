delete from tblAttends
where fnkMemberId in (
    select a.fnkMemberId
    from (select * from tblAttends) a 
    where a.fnkMemberId='0' 
) AND fnkEventId in (
    select b.fnkEventId
    from (select * from tblAttends) b 
    where b.fnkEventId='12' 
);