<template>
    <div>
       <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Tags <Button @click="addModal=true" v-if="isWritePermitted"><Icon type="md-add"/> Add Tag</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Tag Name</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(tag,i) in tags" :key="i" v-if="tags.length">
								<td>{{tag.id}}</td>
								<td class="_table_name">{{tag.tagName}}</td>
								<td>{{tag.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(tag,i)" v-if="isUpdatePermitted">Edit</Button>
									<!-- <Button type="error" size="small" @click="deleteTag(tag,i)" :loading="isDeleting">Delete</Button> -->
									<Button type="error" size="small" @click="showDeletingModal(tag,i)" v-if="isDeletePermitted">Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->
						</table>
					</div>
				</div>

				<!--~~~~~~~ Add Tag Modal ~~~~~~~~~-->
				<Modal v-model="addModal" title="Add Tag" :mask-closable="false" :closable="false" >
					<Input v-model="data.tagName" placeholder="Enter Tag Name Here..."/>
					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="addTag" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding...':'Add Tag'}}</Button>
					</div>
				</Modal>

				<!--~~~~~~~ Edit Tag Modal ~~~~~~~~~-->
				<Modal v-model="editModal" title="Edit Tag" :mask-closable="false" :closable="false" >
					<Input v-model="editData.tagName" placeholder="Edit Tag Name Here..."/>
					<div slot="footer">
						<Button type="default" @click="editModal=false">Close</Button>
						<Button type="primary" @click="editTag" :disabled="isEditing" :loading="isEditing">{{isEditing ? 'Editing...':'Edit Tag'}}</Button>
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
					tagName: '',
				},
				addModal: false,
				editModal: false,
				isAdding: false,
				isEditing: false,
				editData:{
					tagName:'',
				},
				index:-1,
				tags:[],
				showDeleteModal:false,
				deleteItem:{},
				isDeleting:false,
				deleteIndex:-1,
			}
		},

		methods:{
			async addTag(){
				if(this.data.tagName.trim() == '') return this.e('Tag Name is required.');
				this.isAdding = true;
				const res = await this.callApi('post','app/create_tag',this.data)
				if (res.status === 201) {
					this.tags.unshift(res.data)
					this.s('Tag Name has been added successfully!!');
					this.addModal = false
					this.data.tagName = ''
				} else {
					console.log(res)
					if(res.status === 422 ){
						if(res.data.errors.tagName){
							this.e(res.data.errors.tagName[0])
						}
					}else{
						this.swr();
					}
				}
				this.isAdding = false;
			},
			async editTag(){
				if(this.editData.tagName.trim() == '') return this.e('Tag Name is required.');
				this.isEditing = true
				const res = await this.callApi('post','app/edit_tag',this.editData)
				if (res.status === 200) {
					this.tags[this.index].tagName = this.editData.tagName
					this.s('Tag Name has been updated successfully!!');
					this.editModal = false
				}else{
					if(res.status === 422 ){
						if(res.data.errors.tagName){
							this.e(res.data.errors.tagName[0])
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
			showEditModal(tag,index){
				let obj = {
					id : tag.id,
					tagName : tag.tagName
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
			if(!this.isReadPermitted) return 'No Permission';
			const res = await this.callApi('get','/app/get_tags');			
			if(res.status == 200){
				this.tags = res.data
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