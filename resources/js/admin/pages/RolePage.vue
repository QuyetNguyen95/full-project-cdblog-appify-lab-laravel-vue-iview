<template>
    <div>
       <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Roles <Button @click="addModal=true"><Icon type="md-add"/> Add a New Role</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Role Type</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(role,i) in roles" :key="i" v-if="roles.length">
								<td>{{role.id}}</td>
								<td class="_table_name">{{role.roleName}}</td>
								<td>{{role.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(role,i)">Edit</Button>
									<!-- <Button type="error" size="small" @click="deleteTag(tag,i)" :loading="isDeleting">Delete</Button> -->
									<Button type="error" size="small" @click="showDeletingModal(role,i)" >Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->
						</table>
					</div>
				</div>

				<!--~~~~~~~ Add Role Modal ~~~~~~~~~-->
				<Modal v-model="addModal" title="Add Role" :mask-closable="false" :closable="false" >
					<Input v-model="data.roleName" placeholder="Enter Role Name Here..."/>
					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="addRole" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding...':'Add Role'}}</Button>
					</div>
				</Modal>

				<!--~~~~~~~ Edit Role Modal ~~~~~~~~~-->
				<Modal v-model="editModal" title="Edit Role" :mask-closable="false" :closable="false" >
					<Input v-model="editData.roleName" placeholder="Edit Role Name Here..."/>
					<div slot="footer">
						<Button type="default" @click="editModal=false">Close</Button>
						<Button type="primary" @click="editRole" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing...':'Edit Role'}}</Button>
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
					roleName: '',
				},
				addModal: false,
				editModal: false,
				isAdding: false,
				isEditing: false,
				editData:{
					roleName:'',
				},
				index:-1,
				roles:[],
				showDeleteModal:false,
				deleteItem:{},
				isDeleting:false,
				deleteIndex:-1,
			}
		},

		methods:{
			async addRole(){
				if(this.data.roleName.trim() == '') return this.e('Role Name is required.');
				this.isAdding = true;
				const res = await this.callApi('post','app/create_role',this.data)
				if (res.status === 201) {
					this.roles.unshift(res.data)
					this.s('Role has been added successfully!!');
					this.addModal = false
					this.data.roleName = ''
				} else {
					console.log(res)
					if(res.status === 422 ){
						if(res.data.errors.roleName){
							this.e(res.data.errors.roleName[0])
						}
					}else{
						this.swr();
					}
				}
				this.isAdding = false;
			},
			async editRole(){
				if(this.editData.roleName.trim() == '') return this.e('Role Name is required.');
				this.isEditing = true
				const res = await this.callApi('post','app/edit_role',this.editData)
				if (res.status === 200) {
					this.roles[this.index].roleName = this.editData.roleName
					this.s('Role has been updated successfully!!');
					this.editModal = false
				}else{
					if(res.status === 422 ){
						if(res.data.errors.roleName){
							this.e(res.data.errors.roleName[0])
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
			showEditModal(role,index){
				let obj = {
					id : role.id,
					roleName : role.roleName
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
			const res = await this.callApi('get','/app/get_roles');
			if(res.status == 200){
				this.roles = res.data
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