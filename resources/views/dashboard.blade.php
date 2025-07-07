<x-app-layout>
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-2 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="h-[85vh] flex bg-white border rounded shadow overflow-hidden">


        {{-- Modal --}}
        <div id="addContactModal"
            class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded shadow-lg p-6 w-full max-w-sm relative">
                <button class="absolute top-2 right-2 text-gray-500 hover:text-red-600"
                    onclick="document.getElementById('addContactModal').classList.add('hidden')">
                    ✖
                </button>

                <h3 class="text-lg font-bold mb-4 text-gray-800">Ajouter un contact</h3>
                <form method="POST" action="{{ route('invitations.store') }}">
                    @csrf
                    <input type="email" name="email" required placeholder="Email de votre ami"
                        class="w-full border rounded px-3 py-2 mb-4 focus:outline-none focus:ring" />
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                        Ajouter
                    </button>
                </form>
            </div>
        </div>

        {{-- Contacts list (25%) --}}
        <aside class="w-1/4 bg-gray-100 border-r overflow-y-auto p-4">
            @if ($invitations->count() > 0)
                <div class="mb-6 bg-white border border-blue-200 rounded p-4 shadow">
                    <h2 class="text-lg font-bold mb-4 text-blue-700">📨 Invitations reçues</h2>

                    <ul class="space-y-3">
                        @foreach ($invitations as $invitation)
                            <li class="flex justify-between items-center bg-blue-50 px-3 py-2 rounded">
                                <span class="text-gray-800">
                                    {{ $invitation->sender->name }} ({{ $invitation->sender->email }})
                                </span>

                                <div class="flex space-x-2">
                                    <form method="POST" action="{{ route('invitations.accept', $invitation->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-sm">
                                            ✅ Accepter
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('invitations.decline', $invitation->id) }}">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                                            ❌ Refuser
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Bouton Ajouter un contact --}}
            <div class="flex justify-between items-center mb-4">
                <button onclick="document.getElementById('addContactModal').classList.remove('hidden')"
                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                    + Ajouter un contact
                </button>
            </div>
            <h2 class="text-lg font-bold mb-4 text-gray-800">📇 Contacts</h2>

            @forelse($contacts as $c)
                <div
                    class="flex justify-between items-center mb-2 p-2 rounded
            {{ isset($contact) && $contact->id === $c->id ? 'bg-blue-100 text-blue-800' : 'hover:bg-gray-200 text-gray-800' }}">
                    <a href="{{ route('chat.show', $c->id) }}" class="flex-1">
                        {{ $c->name }}
                    </a>

                    {{-- Formulaire de suppression --}}
                    <form action="{{ route('contacts.destroy', $c->id) }}" method="POST"
                        onsubmit="return confirm('Supprimer ce contact ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm ml-2">
                            🗑
                        </button>
                    </form>
                </div>

            @empty
                <p class="text-sm text-gray-500">Aucun contact trouvé.</p>
            @endforelse
        </aside>

        {{-- Conversation (75%) --}}
        <section class="w-3/4 flex flex-col justify-between p-4 relative">
            @isset($contact)
                {{-- Header --}}
                <div class="pb-4 border-b mb-4">
                    <h2 class="text-xl font-semibold text-gray-700">
                        💬 Discussion avec <span class="text-blue-600">{{ $contact->name }}</span>
                    </h2>
                </div>

                {{-- Messages --}}
                <div id="chat-box" class="flex-1 overflow-y-auto space-y-2 pr-2 scroll-smooth">
                    @foreach ($messages as $message)
                        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="max-w-sm px-3 py-2 rounded shadow text-sm
                                {{ $message->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                                {{ $message->content }}
                                <div class="text-[10px] text-right opacity-60 mt-1">
                                    {{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Formulaire d'envoi --}}
                <form x-data="chatForm()" @submit.prevent="sendMessage" class="mt-4 flex items-center space-x-2">
                    <input x-model="message" name="content" placeholder="Écris un message..." required
                        class="w-full border rounded-lg px-4 py-2 text-sm focus:ring focus:ring-blue-200 outline-none" />
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Envoyer
                    </button>
                </form>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-400 text-center px-4">
                    <p>Sélectionne un contact pour commencer une discussion.</p>
                </div>
            @endisset
        </section>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        function chatForm() {
            return {
                message: '',
                sendMessage() {
                    const content = this.message.trim();
                    if (!content) return;

                    this.message = '';

                    fetch("{{ route('chat.send', $contact->id ?? 0) }}", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            content
                        })
                    }).then(response => {
                        if (response.ok) {
                            this.appendMessage(content, true);
                        }
                    }).catch(err => {
                        console.error("Erreur :", err);
                    });
                },
                appendMessage(content, isOwn = false) {
                    const chatBox = document.getElementById('chat-box');
                    const wrapper = document.createElement('div');
                    wrapper.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
                    wrapper.innerHTML = `
                        <div class="max-w-sm px-3 py-2 rounded shadow text-sm ${isOwn ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'}">
                            ${content}
                            <div class="text-[10px] text-right opacity-60 mt-1">maintenant</div>
                        </div>`;
                    chatBox.appendChild(wrapper);
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            };
        }
    </script>

    {{-- Auto scroll to bottom au chargement --}}
    <script>
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>

    {{-- Reverb listener pour les messages reçus --}}
    @isset($contact)
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const currentUserId = @json(auth()->id());
            const contactId = @json($contact->id); // conversation actuellement affichée
                window.Echo.private(`chat.${currentUserId}`)
                .listen('MessageSent', (e) => {

                    // Vérifie si le message vient du contact actuellement affiché
                    if (e.sender_id !== contactId) return;

                    const chatBox = document.getElementById('chat-box');

                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'flex justify-start';
                    msgDiv.innerHTML = `
                        <div class="max-w-sm px-3 py-2 rounded shadow text-sm bg-gray-200 text-gray-800">
                            ${e.content}
                            <div class="text-[10px] text-right opacity-60 mt-1">${e.created_at}</div>
                        </div>
                    `;

                    chatBox.appendChild(msgDiv);
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        });
    </script>
    @endisset


</x-app-layout>
