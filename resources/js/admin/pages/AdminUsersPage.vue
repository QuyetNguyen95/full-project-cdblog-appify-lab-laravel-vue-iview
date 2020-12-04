<template>
    <div>
       <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Users <Button @click="addModal=true"><Icon type="md-add"/> Add user</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Email</th>
								<th>User Type</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(user,i) in users" :key="i" v-if="users.length">
								<td>{{user.id}}</td>
								<td class="_table_name">{{user.fullName}}</td>
								<td >{{user.email}}</td>
								<td >{{user.userType}}</td>
								<td>{{user.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(user,i)">Edit</Button>
									<!-- <Button type="error" size="small" @click="deleteUser(user,i)" :loading="isDeleting">Delete</Button> -->
									<Button type="error" size="small" @click="showDeletingModal(user,i)" >Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->
						</table>
					</div>
				</div>

				<!--~~~~~~~ Add User Modal ~~~~~~~~~-->
				<Modal v-model="addModal" title="Add User" :mask-closable="false" :closable="false" >
					<div class="space">
                        <Input type="text" v-model="data.fullName" placeholder="Full Name"/>
                    </div>
                    <div class="space">
                        <Input type="email" v-model="data.email" placeholder="Email"/>
                    </div>
                    <div class="space">
                        <Input type="password" v-model="data.password" placeholder="Password"/>
                    </div>
                    <div class="space">
                        <Select v-model="data.role_id" placeholder="Select User Type">
                            <Option :value="role.id" v-for="(role,i) in roles" :key="i" v-if="roles.length">{{role.roleName}}</Option>
                        </Select>
                    </div>

					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="addUser" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding...':'Add User'}}</Button>
					</div>
				</Modal>

				<!--~~~~~~~ Edit User Modal ~~~~~~~~~-->
				<Modal v-model="editModal" title="Edit User" :mask-closable="false" :closable="false" >
					<div class="space">
                        <Input type="text" v-model="editData.fullName" placeholder="Full Name"/>
                    </div>
                    <div class="space">
                        <Input type="email" v-model="editData.email" placeholder="Email"/>
                    </div>
                    <div class="space">
                        <Input type="password" v-model="editData.password" placeholder="Password (Optional)"/>
                    </div>
                    <div class="space">
                        <Select v-model="editData.role_id" placeholder="Select User Type">
                            <Option value="Admin">Admin</Option>
                        </Select>
                    </div>
					<div slot="footer">
						<Button type="default" @click="editModal=false">Close</Button>
						<Button type="primary" @click="editUser" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing...':'Edit User'}}</Button>
					</div>
				</Modal>

				<!--~~~~~~~ Delete Tag Alert Modal ~~~~~~~~~-->
				<!-- <Modal v-model="showDeleteModal" width="360">
					<p slot="header" style="color:#f60;text-align:center">
						<Icon type="ios-information-circle"></Icon>
						<span>Delete confirmation</span>
					</p>
					<div style="text-align:center">
						<p>Are you sure you want to delete that?</p>
					</div>
					<div slot="footer">
						<Button type="error" size="large" long @click="deleteTag" :loading="isDeleting" :disabled="isDeleting" >{{isDeleting ? 'Deleting...': 'Delete'}}</Button>
					</div>
				</Modal> -->
				<DeleteModal></DeleteModal>
			</div>
		</div>
    </div>
</template>

<script>
import DeleteModal from '../components/DeleteModal'
import {mapGetters} from 'vuex'
	export default {
		data(){
			return{
				data:{
					fullName: '',
					email: '',
					password: '',
					role_id: null,
				},
				addModal: false,
				editModal: false,
				isAdding: false,
				isEditing: false,
				editData:{
					tagName:'',
				},
				index:-1,
				users:[],
				roles:[],
				showDeleteModal:false,
				deleteItem:{},
				isDeleting:false,
				deleteIndex:-1,
			}
		},

		methods:{
			async addUser(){
				if(this.data.fullName.trim() == '') return this.e('Full Name is required.');
				if(this.data.email.trim() == '') return this.e('Email is required.');
				if(this.data.password.trim() == '') return this.e('Password is required.');
				if(!this.data.role_id) return this.e('User Type is required.');
				this.isAdding = true;
				const res = await this.callApi('post','app/create_user',this.data)
				if (res.status === 201) {
					this.users.unshift(res.data)
					this.s('User has been added successfully!!');
					this.addModal = false
					this.data.tagName = ''
					this.data.email = ''
					this.data.password = ''
					this.data.role_id = null
				} else {
					console.log(res)
					if(res.status === 422 ){
						// if(res.data.errors.tagName){
						// 	this.e(res.data.errors.tagName[0])
                        // }
                        for(let i in res.data.errors){
                            this.e(res.data.errors[i])
                        }
					}else{
						this.swr();
					}
				}
				this.isAdding = false;
			},
			async editUser(){
				if(this.editData.fullName.trim() == '') return this.e('Full Name is required.');
				if(this.editData.email.trim() == '') return this.e('Email is required.');
				if(!this.data.role_id) return this.e('User Type is required.');
				this.isEditing = true
				const res = await this.callApi('post','app/edit_user',this.editData)
				if (res.status === 200) {
					this.users[this.index] = this.editData
					this.s('User has been updated successfully!!');
					this.editModal = false
				}else{
					if(res.status === 422 ){
						 for(let i in res.data.errors){
                            this.e(res.data.errors[i])
                        }
					}else{
						this.swr();
					}
				}
				this.isEditing = false
			},
			// async deleteTag(){
			// 	// if(!confirm('Are you sure to delete?')) return 
			// 	// this.$set(tag,'isDeleting',true)
			// 	this.isDeleting = true;
			// 	const res = await this.callApi('post','/app/delete_tag',this.deleteItem);
			// 	if (res.status == 200) {
			// 		this.tags.splice(this.deleteIndex,1)
			// 		this.s('Tag has been deleted successfully!');
			// 	} else {
			// 		this.swr();
			// 	}
			// 	this.isDeleting = false
			// 	this.showDeleteModal = false;
			// },
			showEditModal(user,index){
				let obj = {
					id : user.id,
					fullName : user.fullName,
					email : user.email,
					role_id : user.role_id,
				}
				this.editData = obj
				this.editModal = true
				this.index = index
			},

			showDeletingModal(tag,i){
				const deleteModalObj = {
					showDeleteModal: true,
					deleteUrl: '/app/delete_tag',
					data: tag,
					deleteIndex:i,
					isDeleted:false,
				}
				this.$store.commit('setDeletingModalObj', deleteModalObj)
				// this.showDeleteModal=true
				// this.deleteItem = tag
				// this.deleteIndex = i
			}
		},
		async created(){
			const [res, resRole] = await Promise.all([
				this.callApi('get','/app/get_users'),
			 	this.callApi('get','/app/get_roles')
			]); 
			
			if(res.status == 200){
				this.users = res.data
			}else{
				this.swr();
			}

			if(resRole.status == 200){
				this.roles = resRole.data
			}else{
				this.swr();
			}
		},
		// async created(){
		// 	const res = await this.callApi('post','/test',{tagName:'Tag Name'});
		// 	console.log(res);
		// 	if(res.status == 200){
		// 	// console.log(res);
		// 	}else{
		// 		console.log(res);
		// 		console.log('Running');
		// 	}
		// },
		computed:{
        	...mapGetters(['getDeleteModalObj'])
    	},
		components:{
			DeleteModal
		},
		watch:{
			getDeleteModalObj(obj){
				console.log(obj)
				if (obj.isDeleted) {
					this.tags.splice(obj.deleteIndex,1)
				}
			}
		}

	}
</script>