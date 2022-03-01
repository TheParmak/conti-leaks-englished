-record(importance,{
	id :: integer(),
	class :: binary(),
	params_bin :: binary(),
	params :: [integer()|binary()],
	preplus :: float(),
	mul :: float(),
	postplus :: float()
}).


