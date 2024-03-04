SELECT grades.itemid  grades.userid
FROM mdl_grade_grade AS grades
INNER JOIN mdl_grade_items as items
ON grades.items = items.id
LIMIT 10;
