<div class="card mb-3">
    <div class="card-body bg-light-primary">
        <div class="row">
            <div class="col-md-auto">
                <div class="avatar avatar-xxl">
                    <img alt="avatar" src="{{ asset('storage/'. $user['image']) }}" class="rounded" />
                </div>
            </div>
            <div class="col-md-8">
                <h5 class="text-secondary">{{ $user['name'] }}</h5>
                @if(!empty($user->main_distributor->name))
                <p class="mb-1"><b>Main Distributors</b> : {{ $user->main_distributor->name }} </p>
                @endif
                @if(!empty($user->distributor->name))
                <p class="mb-1"><b>Distributors</b> : {{ $user->distributor->name }} </p>
                @endif

                @if(!empty($user->userId))
                <p class="mb-1"><b>User Id</b> : {{ $user->userId }} </p>
                @endif
                <p class="mb-1"><b>Email</b> : {{ $user['email'] }}</p>
                <p class="mb-1"><b>Balance</b> : ₹ {{ $user['user_balance'] }}</p>
            </div>
        </div>
    </div>
</div>