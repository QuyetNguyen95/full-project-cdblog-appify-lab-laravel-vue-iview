<template>
    <div>
       <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Assign Role
						 <Select v-model="data.role_id" @on-change="changeAdmin" placeholder="Select User Type" style="width:300px; margin-left:10px;">
                            <Option :value="role.id" v-for="(role,i) in roles" :key="i" v-if="roles.length">{{role.roleName}}</Option>
                        </Select>
					</p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>Resource Name</th>
								<th>Read</th>
								<th>Write</th>
								<th>Update</th>
								<th>Delete</th>
							</tr>
								<!-- TABLE TITLE -->

								<!-- ITEMS -->
							<tr v-for="(r,i) in resources" :key="i">
								<td>{{r.resourceName}}</td>
								<td><Checkbox v-model="r.read"></Checkbox> </td>
								<td><Checkbox v-model="r.write"></Checkbox> </td>
								<td><Checkbox v-model="r.update"></Checkbox> </td>
								<td><Checkbox v-model="r.delete"></Checkbox> </td>

							</tr>
								<!-- ITEMS -->
							<div class="center_button">
								<Button type="primary" @click="assignRoles" :loading="isSending" :disabled="isSending">{{isSending ? "Assigning" : "Assign"}}</Button>
							</div>
						</table>
					</div>
				</div>
			</div>
		</div>
    </div>
</template>

<script>

	export default {
		data(){
			return{
				data:{
					roleName: '',
					role_id: null,
				},
				isSending: false,
				roles:[],
				resources:[
					{resourceName:'Home',read:false, write:false, update:false, delete:false, name:'/'},
					{resourceName:'Tags',read:false, write:false, update:false, delete:false, name:'tags'},
					{resourceName:'Categories',read:false, write:false, update:false, delete:false, name:'categories'},
					{resourceName:'Create Blog',read:false, write:false, update:false, delete:false, name:'createBlog'},
					{resourceName:'Adminusers',read:false, write:false, update:false, delete:false, name:'adminusers'},
					{resourceName:'Roles',read:false, write:false, update:false, delete:false, name:'roles'},
					{resourceName:'Assignrole',read:false, write:false, update:false, delete:false, name:'assignrole'},
				],
				defaultResourcesPermission:[
					{resourceName:'Home',read:false, write:false, update:false, delete:false, name:'/'},
					{resourceName:'Tags',read:false, write:false, update:false, delete:false, name:'tags'},
					{resourceName:'Categories',read:false, write:false, update:false, delete:false, name:'categories'},
					{resourceName:'Create Blog',read:false, write:false, update:false, delete:false, name:'createBlog'},
					{resourceName:'Adminusers',read:false, write:false, update:false, delete:false, name:'adminusers'},
					{resourceName:'Roles',read:false, write:false, update:false, delete:false, name:'roles'},
					{resourceName:'Assignrole',read:false, write:false, update:false, delete:false, name:'assignrole'},

				]
			}
		},

		methods:{
			async assignRoles(){
				this.isSending = true;
				let data = JSON.stringify(this.resources);	

				const res = await this.callApi('post','app/assign_roles',{'permission':data, 'role_id':this.data.role_id});
				if(res.status == 200){
					this.s('Role has been assigned successfully!!');
					let index = this.roles.findIndex(role => role.id == this.data.role_id);
					this.roles[index].permission = data;
				}else{
					this.swr();
				}
				this.isSending = false;

			},
			changeAdmin(){
				let index = this.roles.findIndex(role => role.id == this.data.role_id);
				let permission = this.roles[index].permission;
				
				if (!permission) {
					this.resources = this.defaultResourcesPermission
				} else {
					this.resources = JSON.parse(permission);
				}
			}
		},
		async created(){
			// console.log(this.$route)
			const res = await this.callApi('get','/app/get_roles');
			if(res.status == 200){
				this.roles = res.data
				if (res.data.length) {
					this.data.role_id = res.data[0].id
					if (res.data[0].permission) {
						this.resources = JSON.parse(res.data[0].permission);
					}
				}
			}else{
				this.swr();
			}
		},

	}
</script>