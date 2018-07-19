select * from mp3s where f_artist='';


select * from mp3s where f_md5 in (
	select m
	from (
		select f_size s,f_md5 m,count(*) c
		from mp3s group by f_size,f_md5
	) xx
	where c>1
)
;



# list dup's
select m,c
from (
	select f_md5 m,count(*) c
	from mp3s group by f_md5
) xx
where c>1
;

# total saved space if remove all dup's
select sum(saved) from (
	select *,(c-1)*s saved
	from (
		select f_size s,f_md5 m,count(*) c
		from mp3s group by f_size,f_md5
	) xx
	where c>1
	order by saved
) yy
;


# list dup's
select s,m,c
from (
	select f_size s,f_md5 m,count(*) c
	from mp3s group by f_size,f_md5
) xx
where c>1
;



