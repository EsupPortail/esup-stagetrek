CREATE OR REPLACE PROCEDURE public.update_preferences()
 LANGUAGE plpgsql
AS $procedure$
BEGIN
	update preference p
	set is_sat = u.is_sat
	from v_update_preference u
	where u.preference_id = p.id;
END;
$procedure$