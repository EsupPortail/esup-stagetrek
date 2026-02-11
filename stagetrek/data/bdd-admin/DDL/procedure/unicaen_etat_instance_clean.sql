CREATE OR REPLACE PROCEDURE public.unicaen_etat_instance_clean()
 LANGUAGE plpgsql
AS $procedure$
declare
BEGIN
		DROP table IF EXISTS tmp_etat_instance_not_linked;

		EXECUTE (
			WITH linkers AS (
				SELECT tc.constraint_name
					 , tc.table_name
					 , kcu.column_name
					 , ccu.table_schema AS foreign_table_schema
					 , ccu.table_name   AS foreign_table_name
					 , ccu.column_name  AS foreign_column_name
				FROM information_schema.table_constraints AS tc
					     JOIN information_schema.key_column_usage AS kcu
					     ON tc.constraint_name = kcu.constraint_name
							     AND tc.table_schema = kcu.table_schema
					     JOIN information_schema.constraint_column_usage AS ccu
					     ON ccu.constraint_name = tc.constraint_name
				WHERE tc.constraint_type = 'FOREIGN KEY'
				  AND ccu.table_name = 'unicaen_etat_instance'
			)
			   , requetes AS (
				SELECT 'select ' || column_name || ' from ' || table_name AS sql
				FROM linkers
			)
		   , union_req AS (
				SELECT string_agg( sql, ' union ' ) AS sql
				FROM requetes
			)
			SELECT CONCAT( 'CREATE table tmp_etat_instance_not_linked AS ' ||
				               'select id from unicaen_etat_instance ' ||
				               'except (',
			               ( SELECT sql FROM union_req)
				       , ')'
			       )
		);
--      A décomenter pour exectuer le delete
-- 		DELETE FROM unicaen_etat_instance
-- 		where id in (select * from tmp_etat_instance_not_linked);
--
-- 		DROP table IF EXISTS tmp_etat_instance_not_linked;
end;
$procedure$