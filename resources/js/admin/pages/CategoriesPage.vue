<template>
    <div>
       <div class="content">
			<div class="container-fluid">
				<!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
				<div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
					<p class="_title0">Categories <Button @click="addModal=true" v-if="isWritePermitted"><Icon type="md-add"/> Add Category</Button></p>

					<div class="_overflow _table_div">
						<table class="_table">
								<!-- TABLE TITLE -->
							<tr>
								<th>ID</th>
								<th>Category Name</th>
								<th>Icon Image</th>
								<th>Created At</th>
								<th>Action</th>
							</tr>
								<!-- TABLE TITLE -->


								<!-- ITEMS -->
							<tr v-for="(category,i) in categories" :key="i" v-if="categories.length">
								<td>{{category.id}}</td>
								<td class="_table_name">{{category.categoryName}}</td>
								<td class="table_image">
									<img :src="category.iconImage"/>
								</td>
								<td>{{category.created_at}}</td>
								<td>
									<Button type="info" size="small" @click="showEditModal(category,i)" v-if="isUpdatePermitted">Edit</Button>
									
									<Button type="error" size="small" @click="showDeletingModal(category,i)" v-if="isDeletePermitted">Delete</Button>
								</td>
							</tr>
								<!-- ITEMS -->
						</table>
					</div>
				</div>

				<!--~~~~~~~ Add Category Modal ~~~~~~~~~-->
				<Modal v-model="addModal" title="Add Category" :mask-closable="false" :closable="false" >
					<Input v-model="data.categoryName" placeholder="Enter Category Name Here..."/>
                    <div class="space"></div>
                     <Upload
					 	 ref="uploads"
                         type="drag"
                        :headers="{'x-csrf-token':token, 'X-Requested-With': 'XMLHttpRequest'}"
						:on-success="handleSuccess"
						:on-error="handleError"
						:format="['jpg','jpeg','png']"
						:max-size="2048"
						:on-format-error="handleFormatError"
						:on-exceeded-size="handleMaxSize"
                        action="/app/upload">
                        <div style="padding: 20px 0">
                            <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                            <p>Click or drag files here to upload</p>
                        </div>
                    </Upload>
					
					<div class="demo-upload-list" v-if="data.iconImage">
						<img :src="`${data.iconImage}`">
						<div class="demo-upload-list-cover">
							<Icon type="ios-trash-outline" @click="deleteImage"></Icon>
						</div>
					</div>
					<div slot="footer">
						<Button type="default" @click="addModal=false">Close</Button>
						<Button type="primary" @click="addCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding...':'Add Category'}}</Button>
					</div>
				</Modal>

				<!--~~~~~~~ Edit Category Modal ~~~~~~~~~-->
				<Modal v-model="editModal" title="Edit Category" :mask-closable="false" :closable="false" >
					<Input v-model="editData.categoryName" placeholder="Edit Category Name Here..."/>
                    <div class="space"></div>
                     <Upload
					  	 v-show="isIconImageNew"
					 	 ref="editDataUploads"
                         type="drag"						 
                        :headers="{'x-csrf-token':token, 'X-Requested-With': 'XMLHttpRequest'}"
						:on-success="handleSuccess"
						:on-error="handleError"
						:format="['jpg','jpeg','png']"
						:max-size="2048"
						:on-format-error="handleFormatError"
						:on-exceeded-size="handleMaxSize"
                        action="/app/upload">
                        <div style="padding: 20px 0">
                            <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                            <p>Click or drag files here to upload</p>
                        </div>
                    </Upload>
					
					<div class="demo-upload-list" v-if="editData.iconImage">
						<img :src="`${editData.iconImage}`">
						<div class="demo-upload-list-cover">
							<Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
						</div>
					</div>
					<div slot="footer">
						<Button type="default" @click="closeEditModal">Close</Button>
						<Button type="primary" @click="editCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Editing...':'Edit Category'}}</Button>
					</div>
				</Modal>

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
					categoryName: '',
					iconImage:'',
				},
				addModal: false,
				editModal: false,
				isAdding: false,
				editData:{
					categoryName: '',
					iconImage:'',
				},
				index:-1,
				categories:[],
				showDeleteModal:false,
				deleteItem:{},
				isDeleting:false,
                deleteIndex:-1,
				token:'',
				isIconImageNew:false,
				isEditingItem:false
			}
		},

		methods:{
			async addCategory(){
				if(this.data.categoryName.trim() == '') return this.e('Category Name is required.');
				if(this.data.iconImage.trim() == '') return this.e('Icon Image is required.');
				this.data.iconImage = `${this.data.iconImage}`;
				const res = await this.callApi('post','app/create_category',this.data)
				if (res.status === 201) {
					this.categories.unshift(res.data)
					this.s('Category has been added successfully!!');
					this.addModal = false
					this.data.categoryName = ''
					this.data.iconImage = ''
				} else {
					if(res.status === 422 ){
						if(res.data.errors.categoryName){
							this.e(res.data.errors.categoryName[0])
						}
						if(res.data.errors.iconImage){
							this.e(res.data.errors.iconImage[0])
						}
					}else{
						this.swr();
					}
				}
			},
			async editCategory(){
				if(this.editData.categoryName.trim() == '') return this.e('Category Name is required.');
				if(this.editData.iconImage.trim() == '') return this.e('Icon Image is required.');
				const res = await this.callApi('post','app/edit_category',this.editData)
				if (res.status === 200) {
					this.categories[this.index].categoryName = this.editData.categoryName
					this.s('Category has been updated successfully!!');
					this.editModal = false
				}else{
					if(res.status === 422 ){
						if(res.data.errors.categoryName){
							this.e(res.data.errors.categoryName[0])
						}
						if(res.data.errors.iconImage){
							this.e(res.data.errors.iconImage[0])
						}
					}else{
						this.swr();
					}
				}
			},
			
			async deleteImage(isAdd=true){
				let image;
				if (!isAdd) { //For Editing 
					this.isIconImageNew = true
					image = this.editData.iconImage;
					this.editData.iconImage = ''
                    this.$refs.editDataUploads.clearFiles();
				}else{ // For Adding
					image = this.data.iconImage;
					this.data.iconImage = ''
					this.$refs.uploads.clearFiles();
                }
                
				const res = await this.callApi('post','/app/delete_image',{imageName:image});
				if (res.status != 200) {
					this.data.iconImage = this.image
					this.swr()
				}
			},
			showEditModal(category,index){			
				
				this.editData = category
				this.editModal = true
                this.index = index
                this.isEditingItem = true
			},
            showDeletingModal(category,i){
				const deleteModalObj = {
					showDeleteModal: true,
					deleteUrl: '/app/delete_category',
					data: category,
					deleteIndex:i,
					isDeleted:false,
				}
				this.$store.commit('setDeletingModalObj', deleteModalObj)
				// this.showDeleteModal=true
				// this.deleteItem = tag
				// this.deleteIndex = i
			},
            
			handleSuccess (res, file) {
				res = `/uploads/${res}`;
				if(this.isEditingItem){
					return this.editData.iconImage = res
				}
                this.data.iconImage = res
			},
			handleError (res, file) {
                // console.log('res',res)
				// console.log('file',file)
				this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: `${file.errors.file.length ? file.errors.file[0] : 'Something went wrong.'}`
                });				
            },
            handleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });
            },
            handleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });
			},
			closeEditModal(){
				this.editModal = false
				this.isEditingItem = false

			}
        },
        
		async created(){
			this.token = window.Laravel.csrfToken
			if(!this.isReadPermitted) return 'No Permission';
			const res = await this.callApi('get','/app/get_categories');
			if(res.status == 200){
				this.categories = res.data
			}else{
				this.swr();
			}
		},
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
					this.categories.splice(obj.deleteIndex,1)
				}
			}
		}

	}
</script>

<style>
    .demo-upload-list{
        display: inline-block;
        width: 100px;
        height: 100px;
        text-align: center;
        line-height: 60px;
        border: 1px solid transparent;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,.2);
        margin-right: 4px;
    }
    .demo-upload-list img{
        width: 100%;
        height: 100%;
    }
    .demo-upload-list-cover{
        display: none;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,.6);
    }
    .demo-upload-list:hover .demo-upload-list-cover{
        display: block;
    }
    .demo-upload-list-cover i{
        color: #fff;
        font-size: 30px;
        cursor: pointer;
        margin: 30px 0px 0px 0px;
    }
</style>