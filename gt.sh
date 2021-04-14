if [[ $1 = "push" ]] 
then
	comment=$2
	if [[ $comment = "" ]] 
	then 
		comment="Update"
	fi
	git config --global user.email "n5g11@yahoo.com"
	git config --global user.name "n5g11"
	git add -A
	git commit -m "$comment"
	git push origin master
elif [[ $1 = "pull" ]]  
then
	git pull origin master
else
	echo "Usage : "
	echo "         PUSH - gt.sh push \"comment\""
	echo "         PULL - gt.sh pull"
fi
