import AccountForm from './Form';

export default function Create(props) {
    return <AccountForm {...props} submitUrl={route('social.accounts.store')} />;
}
