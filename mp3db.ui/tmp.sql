select * from 
mp3s 
where f_md5 in ( 
  select f_md5 
  from ( 
    select f_size,f_md5,count(*) c from mp3s group by f_size,f_md5 
  ) cc where c>1 
) order by f_title desc

select *
from mp3s 
group by f_size,f_md5
having count(*) > 1
;


SELECT mp3s.* FROM mp3s INNER JOIN ( 
SELECT f_md5,f_size,COUNT(*) c FROM mp3s GROUP BY f_md5,f_size HAVING COUNT(*)>1) as m2
ON mp3s.f_md5 = m2.f_md5 and mp3s.f_size = m2.f_size
order by f_artist
;
