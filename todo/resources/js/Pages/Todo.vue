<script setup>
import Layout from '@/Layouts/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import DangerButton from '@/Components/DangerButton.vue';
import Checkbox from '@/Components/Checkbox.vue';


// reactive props
const props = defineProps({
    todos: {
        type: Object,
        default: null
    }
})

// reactive form using vue3 composition api and inertiajs
const form = useForm({
    content: '',
})
// reactive editing todo to show update button and update todo
const editingTodo = ref(false);

// reactive checked todos to keep track of checked todos
const checked = ref([]);

// reactive allChecked to check all todos
const allChecked = ref(false);

/**
 * submit form
 */
function submit() {
    // if editing todo is true then update todo
    // else create new todo
    if (editingTodo.value) {
        // update todo
        form.patch(route('todos.update', editingTodo.value), {
            onSuccess: () => {
                // reset form and editing todo
                form.reset();
                editingTodo.value = false;
            },
            preserveScroll: true,
            preserveState: true,
            only: ['todos']
        })
    } else {
        // create new todo
        form.post(route('todos.store'), {
            onSuccess: () => {
                // reset form
                form.reset();

                // reset checked todos and allChecked
                checked.value = [];
                allChecked.value = false;
            },
            preserveScroll: true,
            preserveState: true,
            only: ['todos']
        })
    }
}

/**
 * edit todo
 * change form content to todo content
 * set editing todo to todo id
 * @param {*} todo 
 */
function edit(todo) {
    editingTodo.value = todo.id;
    form.content = todo.content;
}

/**
 * watch allChecked
 */
watch(allChecked, (newValue, oldValue) => {
    // empty checked todos
    checked.value = [];

    // if allChecked is true then push all todos id to checked todos
    if (newValue) {
        // push all todos id to checked todos
        for (const todo of props.todos.data) {
            checked.value.push(todo.id);
        }
    }
})

/**
 * delete all checked todos
 */
function deleteAll() {
    router.delete(route('todos.destroy-many', { todos: checked.value }), {
        preserveScroll: true, preserveState: true,
        onSuccess: () => {
            // empty checked todos
            checked.value = [];
        }
    })
}

/**
 * delete todo
 * @param {*} todo 
 */
function deleteTodo(todo) {
    router.delete(route('todos.destroy', todo), {
        onSuccess: () => {
            // remove todo from checked todos
            checked.value = checked.value.filter(t => t != todo.id)
        },
        preserveScroll: true, preserveState: true
    })
}

</script>

<template>
    <Head>
        <title>Todo App</title>
    </Head>

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight custom-class">Todo</h2>
        </template>

        <div class="py-2">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
                <div>
                    <!-- todo creation form -->
                    <div class="w-full my-6 px-6 py-4 bg-white shadow-sm overflow-hidden rounded-md">
                        <form @submit.prevent="submit" class="flex gap-4">
                            <div class="w-full">
                                <InputLabel for="content" value="Content" />
                                <TextInput id="content" type="text" class="mt-1 block w-full" v-model="form.content"
                                    required placeholder="Enter content.." autocomplete="content" />
                                <InputError class="mt-2" :message="form.errors.content" />
                            </div>
                            <div class="whitespace-nowrap mt-6">
                                <!-- submit button to submit form -->
                                <PrimaryButton class="py-3" :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing">
                                    <!-- update todo if editing todo is true else create new todo -->
                                    {{ editingTodo ? 'Update Todo' : 'Add New Todo' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- table to show the todos. -->
                    <div class="shadow-sm rounded-lg bg-white overflow-hidden">
                        <table class="w-full overflow-hidden">
                            <thead class="bg-blue-500 text-white divide-x border-b">
                                <tr>
                                    <th class="p-4 text-left">
                                        <Checkbox :checked="false" v-model="allChecked" />
                                    </th>
                                    <th class="p-4 text-left">ID</th>
                                    <th class="p-4 text-left">Content</th>
                                    <th class="p-4 text-left">Status</th>
                                    <th class="text-left">
                                        <div class="flex justify-between mx-2 mr-4">
                                            <span class="py-2">Actions</span>
                                            <!-- a button to delete all checked todos -->
                                            <DangerButton type="button" @click="deleteAll" v-if="checked.length">
                                                Delete Marked
                                            </DangerButton>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <!-- use a transition group to animate the list -->
                            <transition-group class="divide-y divide-solid" tag="tbody" name="list">
                                <!-- show todos if todos length is greater than 0 -->
                                <template v-if="todos.data.length">
                                    <tr v-for="todo in todos.data" :key="todo.id" class="hover:bg-gray-200">
                                        <td class="p-4 font-bold">
                                            <!-- a checkbox to check todo for multiple delete -->
                                            <Checkbox :value="todo.id" :checked="checked" v-model="checked" />
                                        </td>
                                        <td class="p-4 font-bold">#{{ todo.id }}</td>
                                        <td class="p-4 w-full">{{ todo.content }}</td>
                                        <td class="p-4">
                                            <!-- show todo status -->
                                            <span class="text-xs rounded-md px-2 py-1 text-white font-semibold"
                                                :class="{ 'bg-emerald-500': todo.status == 'completed', 'bg-orange-500': todo.status == 'incomplete', }">
                                                {{ todo.status }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex gap-2 justify-end">
                                                <!-- a button to complete todo -->
                                                <PrimaryButton type="button"
                                                    @click="router.patch(route('todos.update', todo), { status: 'completed' })"
                                                    v-if="todo.status != 'completed'"
                                                    class="bg-green-500 text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900">
                                                    Complete
                                                </PrimaryButton>
                                                <!-- a button to edit todo -->
                                                <PrimaryButton type="button" @click="edit(todo)">
                                                    Edit
                                                </PrimaryButton>
                                                <!-- a button to delete todo -->
                                                <DangerButton type="button" @click="deleteTodo(todo)">
                                                    Delete
                                                </DangerButton>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <!-- show not found if todos length is 0 -->
                                <template v-else>
                                    <tr key="not-found">
                                        <td colspan="5" class="p-4 text-center text-lg font-bold">No todo left.</td>
                                    </tr>
                                </template>
                                <!-- Pagination -->
                                <tr key="pagination">
                                    <td colspan="5" class="p-4">
                                        <nav aria-label="Page navigation example">
                                            <ul class="flex justify-end">
                                                <li v-for="link in todos.links">
                                                    <Link :class="{ 'bg-blue-500 text-white': link.active }"
                                                        v-if="link.url != null"
                                                        class="block rounded px-3 py-1.5 transition-all duration-300 hover:bg-neutral-300"
                                                        :href="link.url" v-html="link.label">
                                                    </Link>
                                                    <span v-else
                                                        class="block rounded px-3 py-1.5 transition-all duration-300 cursor-not-allowed"
                                                        v-html="link.label"></span>
                                                </li>
                                            </ul>
                                        </nav>
                                    </td>
                                </tr>
                            </transition-group>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style>
.list-enter-active {
    @apply transition-all duration-300 ease-in;
}

.list-leave-active {
    @apply transition-all duration-300 absolute max-w-7xl delay-100 ease-out;
}

.list-move {
    @apply transition-all duration-300 ease-linear delay-100;
}

.list-enter-from,
.list-leave-to {
    @apply opacity-0 scale-50;
}
</style>